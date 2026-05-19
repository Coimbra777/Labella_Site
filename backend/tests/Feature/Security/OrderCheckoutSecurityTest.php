<?php

namespace Tests\Feature\Security;

use App\Jobs\SendNewOrderNotifications;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderCheckoutSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_create_a_valid_order_request_with_server_side_subtotal_and_job_dispatch(): void
    {
        Queue::fake();

        $category = Category::create([
            'name' => 'Vestidos',
            'slug' => 'vestidos',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Vestido Midi',
            'slug' => 'vestido-midi',
            'price' => 59.90,
            'quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Cliente Teste',
            'customer_phone' => '5598999999999',
            'shipping_city' => 'Sao Luis',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
            'notes' => 'Prefiro contato pelo WhatsApp.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Solicitação enviada com sucesso.')
            ->assertJsonPath('order.status', 'pending');

        $orderPayload = $response->json('order');
        $this->assertIsArray($orderPayload);
        $this->assertArrayHasKey('order_number', $orderPayload);
        $this->assertStringStartsWith('ORD-', $orderPayload['order_number']);
        $this->assertEqualsWithDelta(119.80, (float) $orderPayload['subtotal'], 0.01);
        $this->assertEqualsWithDelta(119.80, (float) $orderPayload['total'], 0.01);
        $this->assertArrayNotHasKey('shipping_cost', $orderPayload);
        $this->assertArrayNotHasKey('payment_status', $orderPayload);
        $this->assertCount(1, $orderPayload['items']);
        $item = $orderPayload['items'][0];
        $this->assertSame($product->id, $item['product_id']);
        $this->assertSame('Vestido Midi', $item['name']);
        $this->assertArrayNotHasKey('product', $item);

        $this->assertDatabaseHas('orders', [
            'id' => $response->json('order.id'),
            'user_id' => null,
            'status' => 'pending',
            'payment_status' => 'pending',
            'subtotal' => 119.80,
        ]);

        $this->assertSame(10, $product->fresh()->quantity);
        Queue::assertPushed(SendNewOrderNotifications::class, 1);
    }

    public function test_public_order_request_rejects_sensitive_fields(): void
    {
        $category = Category::create([
            'name' => 'Vestidos',
            'slug' => 'vestidos',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Vestido Midi',
            'slug' => 'vestido-midi',
            'price' => 59.90,
            'quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Cliente Teste',
            'customer_phone' => '5598999999999',
            'shipping_city' => 'Sao Luis',
            'user_id' => 999,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'shipping_cost' => 999.99,
            'discount' => 20,
            'total' => 1,
            'subtotal' => 1,
            'payment_method' => 'pix',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 0.01,
                    'subtotal' => 0.01,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'user_id',
                'status',
                'payment_status',
                'shipping_cost',
                'discount',
                'total',
                'subtotal',
                'payment_method',
                'items.0.price',
                'items.0.subtotal',
            ]);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_public_order_request_validates_stock_without_decrementing_it(): void
    {
        $category = Category::create([
            'name' => 'Blusas',
            'slug' => 'blusas',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Blusa Seda',
            'slug' => 'blusa-seda',
            'price' => 89.90,
            'quantity' => 2,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Cliente Teste',
            'customer_phone' => '5598999999999',
            'shipping_city' => 'Sao Luis',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Verifique os dados do pedido.');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $product->fresh()->quantity);
    }

    public function test_inactive_product_cannot_be_requested(): void
    {
        $category = Category::create([
            'name' => 'Saias',
            'slug' => 'saias',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Saia Midi',
            'slug' => 'saia-midi',
            'price' => 79.90,
            'quantity' => 2,
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Cliente Teste',
            'customer_phone' => '5598999999999',
            'shipping_city' => 'Sao Luis',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Verifique os dados do pedido.');
    }

    public function test_public_order_lookup_routes_are_not_exposed(): void
    {
        $order = Order::factory()->create();

        $this->getJson("/api/v1/orders/{$order->id}")->assertNotFound();
        $this->getJson("/api/v1/orders/number/{$order->order_number}")->assertNotFound();
    }

    public function test_invalid_quantity_is_rejected(): void
    {
        $category = Category::create([
            'name' => 'Conjuntos',
            'slug' => 'conjuntos',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Conjunto Linho',
            'slug' => 'conjunto-linho',
            'price' => 139.90,
            'quantity' => 30,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Cliente Teste',
            'customer_phone' => '5598999999999',
            'shipping_city' => 'Sao Luis',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 0,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.quantity']);
    }

    public function test_payload_with_too_many_items_is_rejected(): void
    {
        $category = Category::create([
            'name' => 'Colecao',
            'slug' => 'colecao',
        ]);

        $items = [];

        foreach (range(1, 21) as $index) {
            $product = Product::create([
                'category_id' => $category->id,
                'name' => 'Produto '.$index,
                'slug' => 'produto-'.$index,
                'price' => 10,
                'quantity' => 10,
                'is_active' => true,
            ]);

            $items[] = [
                'product_id' => $product->id,
                'quantity' => 1,
            ];
        }

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Cliente Teste',
            'customer_phone' => '5598999999999',
            'shipping_city' => 'Sao Luis',
            'items' => $items,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    public function test_public_order_json_never_includes_nested_product_or_stock_fields(): void
    {
        Queue::fake();

        $category = Category::create([
            'name' => 'Vestidos',
            'slug' => 'vestidos',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Vestido Midi',
            'slug' => 'vestido-midi-2',
            'price' => 59.90,
            'quantity' => 99,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_name' => 'Cliente Teste',
            'customer_phone' => '5598999999999',
            'shipping_city' => 'Sao Luis',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertCreated();
        $raw = (string) $response->getContent();
        $this->assertStringNotContainsString('"product":', $raw);
        $this->assertStringNotContainsString('"is_active"', $raw);
        $this->assertStringNotContainsString('"category_id"', $raw);
    }
}
