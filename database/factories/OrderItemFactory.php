<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'menu_id' => Menu::factory(),
            'menu_name' => 'Kopi Susu Gula Aren',
            'price' => 16000,
            'quantity' => 1,
            'subtotal' => 16000,
        ];
    }
}
