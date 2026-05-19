<?php

namespace Tests\Feature\Security;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret123'),
            'is_admin' => true,
        ]);
    }

    private function createOrderWithItem(
        int $productQuantity = 5,
        int $orderQuantity = 2,
        string $status = 'pending'
    ): array {
        $category = Category::create([
            'name' => 'Vestidos',
            'slug' => 'vestidos',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Vestido Festa',
            'slug' => 'vestido-festa-'.fake()->unique()->numerify('###'),
            'price' => 199.90,
            'quantity' => $productQuantity,
            'is_active' => true,
        ]);

        $order = Order::factory()->create([
            'subtotal' => 199.90 * $orderQuantity,
            'total' => 199.90 * $orderQuantity,
            'status' => $status,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'price' => 199.90,
            'quantity' => $orderQuantity,
            'subtotal' => 199.90 * $orderQuantity,
        ]);

        if (in_array($status, ['processing', 'shipped', 'delivered'], true)) {
            $product->decrement('quantity', $orderQuantity);
        }

        return [$order->fresh(), $product->fresh()];
    }

    public function test_non_admin_user_cannot_access_admin_api(): void
    {
        $user = User::create([
            'name' => 'Operador',
            'email' => 'operador@example.com',
            'password' => bcrypt('secret123'),
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->getJson('/api/admin/orders')
            ->assertForbidden();
    }

    public function test_guest_cannot_access_admin_api(): void
    {
        $this->getJson('/api/admin/orders')->assertUnauthorized();
    }

    public function test_admin_bearer_token_can_access_admin_api(): void
    {
        $user = $this->createAdmin();
        $plain = $this->postJson('/api/admin/token', [
            'email' => $user->email,
            'password' => 'secret123',
        ])
            ->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'user'])
            ->json('token');

        $this->withToken($plain)->getJson('/api/admin/orders')->assertOk();
    }

    public function test_non_admin_cannot_issue_api_token(): void
    {
        $user = User::create([
            'name' => 'Operador',
            'email' => 'operador@example.com',
            'password' => bcrypt('secret123'),
            'is_admin' => false,
        ]);

        $this->postJson('/api/admin/token', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertForbidden();
    }

    public function test_token_issue_with_invalid_credentials_returns_401(): void
    {
        $user = $this->createAdmin();
        $this->postJson('/api/admin/token', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    public function test_non_admin_bearer_token_cannot_access_admin_api(): void
    {
        $user = User::create([
            'name' => 'Operador',
            'email' => 'operador@example.com',
            'password' => bcrypt('secret123'),
            'is_admin' => false,
        ]);
        $plain = $user->createToken('test-client')->plainTextToken;

        $this->withToken($plain)
            ->getJson('/api/admin/orders')
            ->assertForbidden();
    }

    public function test_admin_can_revoke_current_bearer_token(): void
    {
        $user = $this->createAdmin();
        $plain = $this->postJson('/api/admin/token', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertOk()->json('token');

        $this->withToken($plain)
            ->deleteJson('/api/admin/token')
            ->assertNoContent();

        $this->withToken($plain)
            ->getJson('/api/admin/orders')
            ->assertUnauthorized();
    }

    public function test_admin_user_can_access_admin_api(): void
    {
        $user = $this->createAdmin();

        $this->actingAs($user)
            ->getJson('/api/admin/orders')
            ->assertOk();
    }

    public function test_public_upload_routes_are_not_exposed(): void
    {
        $this->postJson('/api/v1/upload/image')->assertNotFound();
        $this->deleteJson('/api/v1/upload/image', ['path' => 'images/test.png'])->assertNotFound();
    }

    public function test_pending_to_processing_decrements_stock_once(): void
    {
        $admin = $this->createAdmin();
        [$order, $product] = $this->createOrderWithItem();

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'processing',
            ])
            ->assertOk()
            ->assertJsonPath('order.status', 'processing');

        $this->assertSame(3, $product->fresh()->quantity);
    }

    public function test_pending_to_cancelled_does_not_change_stock(): void
    {
        $admin = $this->createAdmin();
        [$order, $product] = $this->createOrderWithItem();

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'cancelled',
            ])
            ->assertOk()
            ->assertJsonPath('order.status', 'cancelled');

        $this->assertSame(5, $product->fresh()->quantity);
    }

    public function test_processing_to_cancelled_returns_stock_once(): void
    {
        $admin = $this->createAdmin();
        [$order, $product] = $this->createOrderWithItem(status: 'processing');

        $this->assertSame(3, $product->quantity);

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'cancelled',
            ])
            ->assertOk()
            ->assertJsonPath('order.status', 'cancelled');

        $this->assertSame(5, $product->fresh()->quantity);

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'cancelled',
            ])
            ->assertOk()
            ->assertJsonPath('order.status', 'cancelled');

        $this->assertSame(5, $product->fresh()->quantity);
    }

    public function test_processing_to_processing_does_not_change_stock_again(): void
    {
        $admin = $this->createAdmin();
        [$order, $product] = $this->createOrderWithItem(status: 'processing');

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'processing',
                'notes' => 'Atualizacao operacional.',
            ])
            ->assertOk()
            ->assertJsonPath('order.status', 'processing');

        $this->assertSame(3, $product->fresh()->quantity);
    }

    public function test_cancelled_to_processing_validates_stock_and_decrements_it(): void
    {
        $admin = $this->createAdmin();
        [$order, $product] = $this->createOrderWithItem(status: 'cancelled');

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'processing',
            ])
            ->assertOk()
            ->assertJsonPath('order.status', 'processing');

        $this->assertSame(3, $product->fresh()->quantity);
    }

    public function test_processing_to_pending_returns_stock(): void
    {
        $admin = $this->createAdmin();
        [$order, $product] = $this->createOrderWithItem(status: 'processing');

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'pending',
            ])
            ->assertOk()
            ->assertJsonPath('order.status', 'pending');

        $this->assertSame(5, $product->fresh()->quantity);
    }

    public function test_delivered_to_cancelled_is_blocked(): void
    {
        $admin = $this->createAdmin();
        [$order, $product] = $this->createOrderWithItem(status: 'delivered');

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'cancelled',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertSame(3, $product->fresh()->quantity);
    }

    public function test_order_without_sufficient_stock_cannot_move_to_processing(): void
    {
        $admin = $this->createAdmin();
        [$order, $product] = $this->createOrderWithItem(productQuantity: 1, orderQuantity: 2, status: 'pending');

        $this->actingAs($admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'processing',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertSame(1, $product->fresh()->quantity);
    }
}
