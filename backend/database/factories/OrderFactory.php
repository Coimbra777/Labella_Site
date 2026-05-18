<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . strtoupper(fake()->bothify('##########')),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'shipping_address' => fake()->streetAddress(),
            'shipping_city' => fake()->city(),
            'shipping_state' => fake()->state(),
            'shipping_zip' => fake()->postcode(),
            'shipping_country' => 'BR',
            'subtotal' => 100,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => null,
        ];
    }
}
