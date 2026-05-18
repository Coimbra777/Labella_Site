<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewOrderNotifications;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private const MAX_ITEMS = 20;

    private const MAX_ITEM_QUANTITY = 20;

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'nullable|string',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_zip' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:2',
            'items' => 'required|array|min:1|max:' . self::MAX_ITEMS,
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:' . self::MAX_ITEM_QUANTITY,
            'items.*.size' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            'items.*.price' => 'prohibited',
            'items.*.subtotal' => 'prohibited',
            'user_id' => 'prohibited',
            'order_number' => 'prohibited',
            'status' => 'prohibited',
            'payment_status' => 'prohibited',
            'shipping_cost' => 'prohibited',
            'discount' => 'prohibited',
            'total' => 'prohibited',
            'subtotal' => 'prohibited',
            'payment_method' => 'prohibited',
            'created_at' => 'prohibited',
            'updated_at' => 'prohibited',
            'deleted_at' => 'prohibited',
            'notes' => 'nullable|string',
        ], [
            'customer_name.required' => 'O nome completo é obrigatório.',
            'customer_phone.required' => 'O telefone é obrigatório.',
            'customer_email.email' => 'Informe um e-mail válido.',
            'shipping_city.required' => 'A cidade é obrigatória.',
            'items.required' => 'O carrinho está vazio. Adicione produtos antes de finalizar.',
            'items.min' => 'O carrinho está vazio. Adicione produtos antes de finalizar.',
            'items.max' => 'Limite máximo de ' . self::MAX_ITEMS . ' itens por solicitação.',
            'items.*.product_id.required' => 'Produto inválido no carrinho.',
            'items.*.product_id.exists' => 'Um ou mais produtos não foram encontrados.',
            'items.*.quantity.required' => 'Informe a quantidade do produto.',
            'items.*.quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'items.*.quantity.min' => 'A quantidade mínima é 1.',
            'items.*.quantity.max' => 'A quantidade máxima por item é ' . self::MAX_ITEM_QUANTITY . '.',
            'items.*.price.prohibited' => 'O preço dos itens é calculado pelo servidor.',
            'items.*.subtotal.prohibited' => 'O subtotal dos itens é calculado pelo servidor.',
            'user_id.prohibited' => 'Campos administrativos não podem ser enviados na solicitação.',
            'order_number.prohibited' => 'Campos administrativos não podem ser enviados na solicitação.',
            'status.prohibited' => 'Campos administrativos não podem ser enviados na solicitação.',
            'payment_status.prohibited' => 'Campos administrativos não podem ser enviados na solicitação.',
            'shipping_cost.prohibited' => 'Campos financeiros são definidos pelo administrador.',
            'discount.prohibited' => 'Campos financeiros são definidos pelo administrador.',
            'total.prohibited' => 'Campos financeiros são definidos pelo administrador.',
            'subtotal.prohibited' => 'Campos financeiros são definidos pelo administrador.',
            'payment_method.prohibited' => 'A forma de pagamento é definida posteriormente pelo administrador.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Verifique os dados do formulário.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validated = $validator->validated();

            $order = DB::transaction(function () use ($validated) {
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

                    if (!$product || !$product->is_active) {
                        throw ValidationException::withMessages([
                            'items' => ["O produto informado nao esta disponivel para compra."],
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

                return $order->load('items.product');
            });

            try {
                SendNewOrderNotifications::dispatch($order->id)->afterCommit();
            } catch (\Throwable $e) {
                report($e);
            }

            $order->load('items.product');

            return response()->json([
                'message' => 'Solicitação enviada com sucesso.',
                'order' => $order
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Verifique os dados do pedido.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Não foi possível criar a solicitação.',
            ], 500);
        }
    }
}
