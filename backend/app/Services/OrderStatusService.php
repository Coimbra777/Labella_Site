<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    private const ALLOWED_TRANSITIONS = [
        Order::STATUS_PENDING => [
            Order::STATUS_PROCESSING,
            Order::STATUS_CANCELLED,
        ],
        Order::STATUS_PROCESSING => [
            Order::STATUS_PENDING,
            Order::STATUS_SHIPPED,
            Order::STATUS_CANCELLED,
        ],
        Order::STATUS_SHIPPED => [
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
        ],
        Order::STATUS_DELIVERED => [],
        Order::STATUS_CANCELLED => [
            Order::STATUS_PROCESSING,
        ],
    ];

    public function update(Order $order, array $attributes): Order
    {
        return DB::transaction(function () use ($order, $attributes) {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            $originalStatus = $lockedOrder->status;
            $nextStatus = $attributes['status'] ?? $originalStatus;

            $this->assertAllowedTransition($originalStatus, $nextStatus);

            $shippingCost = array_key_exists('shipping_cost', $attributes)
                ? (float) $attributes['shipping_cost']
                : (float) $lockedOrder->shipping_cost;
            $discount = array_key_exists('discount', $attributes)
                ? (float) $attributes['discount']
                : (float) $lockedOrder->discount;

            $attributes['total'] = max(0, (float) $lockedOrder->subtotal + $shippingCost - $discount);

            if (!$lockedOrder->reservesInventory($originalStatus) && $lockedOrder->reservesInventory($nextStatus)) {
                $this->reserveInventory($lockedOrder);
            }

            if ($lockedOrder->reservesInventory($originalStatus) && !$lockedOrder->reservesInventory($nextStatus)) {
                $this->releaseInventory($lockedOrder);
            }

            $lockedOrder->fill($attributes);
            $lockedOrder->save();

            return $lockedOrder->fresh(['items.product']);
        });
    }

    public function statusOptionsFor(?Order $order = null): array
    {
        if (!$order) {
            return Order::statusOptions();
        }

        $options = [$order->status => Order::statusOptions()[$order->status] ?? $order->status];

        foreach (self::ALLOWED_TRANSITIONS[$order->status] ?? [] as $status) {
            $options[$status] = Order::statusOptions()[$status] ?? $status;
        }

        return $options;
    }

    private function assertAllowedTransition(string $from, string $to): void
    {
        if ($from === $to) {
            return;
        }

        if (!in_array($to, self::ALLOWED_TRANSITIONS[$from] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => ['Transição de status inválida para este pedido.'],
            ]);
        }
    }

    private function reserveInventory(Order $order): void
    {
        $itemsByProduct = $order->items
            ->filter(fn ($item) => $item->product_id !== null)
            ->groupBy('product_id');

        $products = Product::query()
            ->whereIn('id', $itemsByProduct->keys())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($itemsByProduct as $productId => $items) {
            $product = $products->get((int) $productId);
            $requestedQuantity = (int) $items->sum('quantity');
            $productName = (string) $items->first()->product_name;

            if (!$product || !$product->is_active) {
                throw ValidationException::withMessages([
                    'status' => ["O item {$productName} nao esta mais disponivel para confirmacao."],
                ]);
            }

            if ($product->quantity < $requestedQuantity) {
                throw ValidationException::withMessages([
                    'status' => ["Estoque insuficiente para confirmar {$productName}."],
                ]);
            }
        }

        foreach ($itemsByProduct as $productId => $items) {
            $products->get((int) $productId)?->decrement('quantity', (int) $items->sum('quantity'));
        }
    }

    private function releaseInventory(Order $order): void
    {
        $itemsByProduct = $order->items
            ->filter(fn ($item) => $item->product_id !== null)
            ->groupBy('product_id');

        $products = Product::query()
            ->whereIn('id', $itemsByProduct->keys())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($itemsByProduct as $productId => $items) {
            $products->get((int) $productId)?->increment('quantity', (int) $items->sum('quantity'));
        }
    }
}
