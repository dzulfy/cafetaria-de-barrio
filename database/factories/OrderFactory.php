<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_code' => 'DB-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'table_number' => '05',
            'customer_name' => fake()->name(),
            'notes' => fake()->optional()->sentence(),
            'subtotal' => 42000,
            'total_price' => 42000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
        ];
    }
}
