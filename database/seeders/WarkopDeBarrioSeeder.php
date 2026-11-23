<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WarkopDeBarrioSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tables
        $tables = [
            'Meja 01', 'Meja 02', 'Meja 03', 'Meja 04',
            'Meja 05', 'Meja 06', 'Meja 07', 'Meja 08',
        ];

        foreach ($tables as $tbl) {
            RestaurantTable::firstOrCreate(['table_number' => $tbl], ['status' => 'available']);
        }

        // 2. Categories
        $catKopi = Category::firstOrCreate(['slug' => 'kopi'], ['name' => 'Kopi', 'icon' => 'coffee']);
        $catNonKopi = Category::firstOrCreate(['slug' => 'non-kopi'], ['name' => 'Non-Kopi', 'icon' => 'cup']);
        $catMakanan = Category::firstOrCreate(['slug' => 'makanan'], ['name' => 'Makanan', 'icon' => 'utensils']);
        $catCamilan = Category::firstOrCreate(['slug' => 'camilan'], ['name' => 'Camilan', 'icon' => 'cookie']);

        // 3. Menu Items
        $menus = [
            // Kopi
            [
                'category_id' => $catKopi->id,
                'name' => 'Kopi Susu Gula Aren',
                'description' => 'Espresso robusta & arabica blend dengan susu creamy dan gula aren organik',
                'price' => 16000,
                'is_available' => true,
            ],
            [
                'category_id' => $catKopi->id,
                'name' => 'Americano',
                'description' => 'Double shot espresso dengan air dingin dan es batu segar',
                'price' => 14000,
                'is_available' => true,
            ],
            [
                'category_id' => $catKopi->id,
                'name' => 'Caramel Macchiato',
                'description' => 'Espresso dengan sirup vanila, susu berbusa, dan drizzle saus karamel lezat',
                'price' => 22000,
                'is_available' => true,
            ],
            [
                'category_id' => $catKopi->id,
                'name' => 'Cafe Latte',
                'description' => 'Kombinasi klasik espresso lembut dengan steamed milk gurih',
                'price' => 18000,
                'is_available' => true,
            ],
            [
                'category_id' => $catKopi->id,
                'name' => 'Kopi Tubruk De Barrio',
                'description' => 'Kopi hitam tradisional khas Nusantara dengan aroma rempah harum',
                'price' => 10000,
                'is_available' => true,
            ],
            // Non-Kopi
            [
                'category_id' => $catNonKopi->id,
                'name' => 'Matcha Latte',
                'description' => 'Bubuk matcha murni Jepang berpadu dengan susu segar lembut',
                'price' => 20000,
                'is_available' => true,
            ],
            [
                'category_id' => $catNonKopi->id,
                'name' => 'Chocolate Signature',
                'description' => 'Cokelat Belgia pekat dengan rasa manis gurih yang pas di lidah',
                'price' => 20000,
                'is_available' => true,
            ],
            [
                'category_id' => $catNonKopi->id,
                'name' => 'Lemon Tea Segar',
                'description' => 'Teh hitam pilihan disajikan dengan perasan jeruk lemon asli',
                'price' => 12000,
                'is_available' => true,
            ],
            // Makanan
            [
                'category_id' => $catMakanan->id,
                'name' => 'Mie Nyemek De Barrio',
                'description' => 'Mie kuah kental bumbu rempah spesial, sayuran, dan telur setengah matang',
                'price' => 18000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMakanan->id,
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng kampung dengan suwiran ayam, sosis, dan telur ceplok',
                'price' => 25000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMakanan->id,
                'name' => 'Ayam Geprek Sambal Korek',
                'description' => 'Ayam krispi renyah diulek dengan cabai rawit pedas mantap',
                'price' => 23000,
                'is_available' => true,
            ],
            // Camilan
            [
                'category_id' => $catCamilan->id,
                'name' => 'Pisang Goreng',
                'description' => 'Pisang kepok manis digoreng tepung renyah dengan taburan gula aren halus',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catCamilan->id,
                'name' => 'Roti Bakar Coklat Keju',
                'description' => 'Roti tebal panggang renyah isi meses coklat melimpah dan keju parut gurih',
                'price' => 15000,
                'is_available' => true,
            ],
            [
                'category_id' => $catCamilan->id,
                'name' => 'Cireng Krispi Sambal Rujak',
                'description' => 'Cireng gurih kenyal renyah di luar dicocol bumbu rujak pedas manis',
                'price' => 12000,
                'is_available' => true,
            ],
        ];

        $menuMap = [];
        foreach ($menus as $m) {
            $created = MenuItem::updateOrCreate(
                ['name' => $m['name']],
                $m
            );
            $menuMap[$m['name']] = $created;
        }

        // 4. Figma mockup Orders
        // Order 1: DB-0124 (Selected in Figma)
        $order1 = Order::updateOrCreate(
            ['order_code' => 'DB-0124'],
            [
                'table_number' => 'Meja 05',
                'customer_name' => 'Bima',
                'source' => 'QR Meja',
                'status' => 'menunggu',
                'customer_notes' => 'Gula sedikit, karna perasaan bisa berubah tapi kopi harus tetap manis.',
                'total_amount' => 42000,
                'created_at' => Carbon::now()->subMinutes(2),
            ]
        );
        $order1->items()->delete();
        $order1->items()->createMany([
            [
                'menu_item_id' => $menuMap['Kopi Susu Gula Aren']->id ?? null,
                'menu_name' => 'Kopi Susu Gula Aren',
                'price' => 16000,
                'quantity' => 2,
                'subtotal' => 32000,
            ],
            [
                'menu_item_id' => $menuMap['Pisang Goreng']->id ?? null,
                'menu_name' => 'Pisang Goreng',
                'price' => 10000,
                'quantity' => 1,
                'subtotal' => 10000,
            ],
        ]);

        // Order 2: DB-0123
        $order2 = Order::updateOrCreate(
            ['order_code' => 'DB-0123'],
            [
                'table_number' => 'Meja 02',
                'customer_name' => 'Andi',
                'source' => 'QR Meja',
                'status' => 'menunggu',
                'customer_notes' => 'Dingin, es batu dibanyakin ya kak.',
                'total_amount' => 25000,
                'created_at' => Carbon::now()->subMinutes(6),
            ]
        );
        $order2->items()->delete();
        $order2->items()->createMany([
            [
                'menu_item_id' => $menuMap['Nasi Goreng Spesial']->id ?? null,
                'menu_name' => 'Nasi Goreng Spesial',
                'price' => 25000,
                'quantity' => 1,
                'subtotal' => 25000,
            ],
        ]);

        // Order 3: DB-0122
        $order3 = Order::updateOrCreate(
            ['order_code' => 'DB-0122'],
            [
                'table_number' => 'Meja 01',
                'customer_name' => 'Citra',
                'source' => 'QR Meja',
                'status' => 'menunggu',
                'customer_notes' => 'Mienya jangan terlalu pedas ya mas.',
                'total_amount' => 61000,
                'created_at' => Carbon::now()->subMinutes(12),
            ]
        );
        $order3->items()->delete();
        $order3->items()->createMany([
            [
                'menu_item_id' => $menuMap['Mie Nyemek De Barrio']->id ?? null,
                'menu_name' => 'Mie Nyemek De Barrio',
                'price' => 18000,
                'quantity' => 1,
                'subtotal' => 18000,
            ],
            [
                'menu_item_id' => $menuMap['Matcha Latte']->id ?? null,
                'menu_name' => 'Matcha Latte',
                'price' => 20000,
                'quantity' => 1,
                'subtotal' => 20000,
            ],
            [
                'menu_item_id' => $menuMap['Ayam Geprek Sambal Korek']->id ?? null,
                'menu_name' => 'Ayam Geprek Sambal Korek',
                'price' => 23000,
                'quantity' => 1,
                'subtotal' => 23000,
            ],
        ]);

        // Order 4: DB-0121 (Diproses)
        $order4 = Order::updateOrCreate(
            ['order_code' => 'DB-0121'],
            [
                'table_number' => 'Meja 04',
                'customer_name' => 'Fahri',
                'source' => 'Kasir',
                'status' => 'diproses',
                'customer_notes' => 'Dibungkus terpisah ya.',
                'total_amount' => 37000,
                'created_at' => Carbon::now()->subMinutes(20),
            ]
        );
        $order4->items()->delete();
        $order4->items()->createMany([
            [
                'menu_item_id' => $menuMap['Caramel Macchiato']->id ?? null,
                'menu_name' => 'Caramel Macchiato',
                'price' => 22000,
                'quantity' => 1,
                'subtotal' => 22000,
            ],
            [
                'menu_item_id' => $menuMap['Roti Bakar Coklat Keju']->id ?? null,
                'menu_name' => 'Roti Bakar Coklat Keju',
                'price' => 15000,
                'quantity' => 1,
                'subtotal' => 15000,
            ],
        ]);

        // Order 5: DB-0120 (Selesai)
        $order5 = Order::updateOrCreate(
            ['order_code' => 'DB-0120'],
            [
                'table_number' => 'Meja 03',
                'customer_name' => 'Dina',
                'source' => 'QR Meja',
                'status' => 'selesai',
                'customer_notes' => 'Gula normal.',
                'total_amount' => 38000,
                'payment_method' => 'tunai',
                'amount_paid' => 50000,
                'change_amount' => 12000,
                'paid_at' => Carbon::now()->subMinutes(45),
                'created_at' => Carbon::now()->subMinutes(50),
            ]
        );
        $order5->items()->delete();
        $order5->items()->createMany([
            [
                'menu_item_id' => $menuMap['Cafe Latte']->id ?? null,
                'menu_name' => 'Cafe Latte',
                'price' => 18000,
                'quantity' => 1,
                'subtotal' => 18000,
            ],
            [
                'menu_item_id' => $menuMap['Chocolate Signature']->id ?? null,
                'menu_name' => 'Chocolate Signature',
                'price' => 20000,
                'quantity' => 1,
                'subtotal' => 20000,
            ],
        ]);
    }
}
