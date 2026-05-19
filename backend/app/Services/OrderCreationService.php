<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Cria pedidos públicos (solicitação/orçamento): pending, totais no servidor, sem baixar estoque.
 */
class OrderCreationService
{
    /**
     * @param  array<string, mixed>  $validated  Dados validados de {@see \App\Http\Requests\Api\StorePublicOrderRequest}
     */
    public function createPendingPublicOrder(array $validated): Order
    {
        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $items = [];
            $requestedQuantities = [];
            $productIds = collect($validated['items'])
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            foreach ($validated['items'] as $itemData) {
                $productId = (int) $itemData['product_id'];
                $requestedQuantities[$productId] = ($requestedQuantities[$productId] ?? 0) + (int) $itemData['quantity'];
                $product = $products->get($productId);

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ['O produto informado nao esta disponivel para compra.'],
                    ]);
                }

                if ($product->quantity < $requestedQuantities[$productId]) {
                    throw ValidationException::withMessages([
                        'items' => ["Estoque insuficiente para o produto {$product->name}."],
                    ]);
                }

                $itemPrice = (float) $product->price;
                $itemSubtotal = $itemPrice * (int) $itemData['quantity'];
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product' => $product,
                    'data' => $itemData,
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $shippingCost = 0.0;
            $discount = 0.0;
            $total = $subtotal + $shippingCost - $discount;

            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'] ?? null,
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'] ?? null,
                'shipping_zip' => $validated['shipping_zip'] ?? null,
                'shipping_country' => $validated['shipping_country'] ?? 'BR',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_sku' => $item['product']->sku,
                    'price' => $item['price'],
                    'quantity' => $item['data']['quantity'],
                    'subtotal' => $item['subtotal'],
                    'size' => $item['data']['size'] ?? null,
                    'color' => $item['data']['color'] ?? null,
                ]);
            }

            return $order->load('items');
        });
    }
}
