<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = Order::firstOrCreate(
            ['order_code' => 'DB-0124'],
            [
                'table_number' => '05',
                'customer_name' => 'Rina',
                'notes' => 'gula sedikit',
                'subtotal' => 42000,
                'total_price' => 42000,
                'status' => 'menunggu',
                'payment_status' => 'unpaid',
            ]
        );

        $kopi = Menu::where('slug', 'kopi-susu-gula-aren')->first();
        $pisang = Menu::where('slug', 'pisang-goreng')->first();

        if ($kopi && ! $order->items()->where('menu_name', $kopi->name)->exists()) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $kopi->id,
                'menu_name' => $kopi->name,
                'price' => $kopi->price,
                'quantity' => 2,
                'subtotal' => $kopi->price * 2,
            ]);
        }

        if ($pisang && ! $order->items()->where('menu_name', $pisang->name)->exists()) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $pisang->id,
                'menu_name' => $pisang->name,
                'price' => $pisang->price,
                'quantity' => 1,
                'subtotal' => $pisang->price * 1,
            ]);
        }
    }
}
