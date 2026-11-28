<?php

namespace Database\Seeders;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\SyncQueue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WarkopDeBarrioSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tables (canonical cafe_tables)
        $tables = [
            ['table_number' => '01', 'name' => 'Meja 01', 'token' => 'x7Kp9aLm2Q', 'status' => 'aktif'],
            ['table_number' => '02', 'name' => 'Meja 02', 'token' => 'b3Td8vNq5R', 'status' => 'aktif'],
            ['table_number' => '03', 'name' => 'Meja 03', 'token' => 'm9Ws1cHy7E', 'status' => 'aktif'],
            ['table_number' => '04', 'name' => 'Meja 04', 'token' => 'k2Lf6zXp4A', 'status' => 'aktif'],
            ['table_number' => '05', 'name' => 'Meja 05', 'token' => 'p8Gh3uJr1N', 'status' => 'aktif'],
            ['table_number' => '06', 'name' => 'Meja 06', 'token' => 't5Yv7eBd9C', 'status' => 'aktif'],
            ['table_number' => '07', 'name' => 'Meja 07', 'token' => 'w1Qn4sKm6Z', 'status' => 'aktif'],
            ['table_number' => '08', 'name' => 'Meja 08', 'token' => 'h6Rj2xFa8P', 'status' => 'nonaktif'],
        ];

        foreach ($tables as $tbl) {
            CafeTable::firstOrCreate(['table_number' => $tbl['table_number']], $tbl);
        }

        // 2. Categories
        $catMinuman = Category::firstOrCreate(['slug' => 'minuman'], ['name' => 'Minuman', 'icon' => 'coffee']);
        $catSnack = Category::firstOrCreate(['slug' => 'snack'], ['name' => 'Snack', 'icon' => 'food']);
        $catMie = Category::firstOrCreate(['slug' => 'mie'], ['name' => 'Mie', 'icon' => 'food']);
        $catAddOn = Category::firstOrCreate(['slug' => 'add-on'], ['name' => 'Add On', 'icon' => 'food']);

        // 3. Menus (29 item menu asli Cafetaria De Barrio pada tabel canonical `menus`)

        $menus = [
            // Minuman (14 item)
            [
                'category_id' => $catMinuman->id,
                'name' => 'Nescafe',
                'description' => 'Kopi instan Nescafe klasik dengan aroma mantap, disajikan hangat atau dingin.',
                'price' => 7000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Josu',
                'description' => 'Perpaduan segar Extra Joss dengan susu kental manis yang creamy.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Nutrisari',
                'description' => 'Minuman buah segar rasa jeruk pilihan pelepas dahaga.',
                'price' => 7000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Lemon Tea',
                'description' => 'Teh pilihan berpadu dengan kesegaran rasa lemon yang menyegarkan.',
                'price' => 8000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Teh Tarik',
                'description' => 'Teh susu khas ala warkop dengan tekstur lembut dan rasa manis pas.',
                'price' => 8000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Matcha',
                'description' => 'Minuman green tea matcha creamy yang lembut dan menenangkan.',
                'price' => 8000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Drink Beng Beng',
                'description' => 'Minuman cokelat karamel malt nikmat ala Beng-Beng favorit anak kos.',
                'price' => 9000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Kukubima Susu',
                'description' => 'Minuman energi Kukubima berpadu dengan susu kental manis penyegar stamina.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Goodday Freeze',
                'description' => 'Kopi dingin Good Day Freeze yang menyegarkan untuk teman nongkrong.',
                'price' => 9000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Goodday Cappucino',
                'description' => 'Kopi cappuccino nikmat dengan taburan choco granule di atasnya.',
                'price' => 9000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'ABC Klepon',
                'description' => 'Minuman kopi susu unik dengan cita rasa pandan gula aren khas klepon.',
                'price' => 9000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Teajus',
                'description' => 'Es teh manis hemat dan menyegarkan untuk menemani makanan.',
                'price' => 6000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Dancow Putih',
                'description' => 'Susu Dancow putih gurih dan bergizi, nikmat diminum hangat atau es.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Dancow Coklat',
                'description' => 'Susu cokelat Dancow manis creamy favorit semua kalangan.',
                'price' => 10000,
                'is_available' => true,
            ],

            // Snack (4 item)
            [
                'category_id' => $catSnack->id,
                'name' => 'French Fries',
                'description' => 'Kentang goreng renyah di luar, lembut di dalam, lengkap dengan saus sambal.',
                'price' => 12000,
                'is_available' => true,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Nugget',
                'description' => 'Nugget ayam goreng krispi gurih untuk teman ngobrol.',
                'price' => 12000,
                'is_available' => true,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Sosis',
                'description' => 'Sosis goreng gurih siap santap lengkap dengan cocolan saus.',
                'price' => 12000,
                'is_available' => true,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Mix Platter',
                'description' => 'Paket lengkap berisi kentang goreng, sosis, dan nugget dalam satu porsi.',
                'price' => 15000,
                'is_available' => true,
            ],

            // Mie (8 item)
            [
                'category_id' => $catMie->id,
                'name' => 'Indomie Goreng',
                'description' => 'Indomie goreng legendaris dengan bawang goreng renyah dan bumbu autentik.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMie->id,
                'name' => 'Indomie Ayam Bawang',
                'description' => 'Indomie kuah hangat rasa ayam bawang yang gurih menenangkan.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMie->id,
                'name' => 'Indomie Aceh',
                'description' => 'Indomie goreng mie tebal berbumbu rempah khas Aceh yang kaya rasa.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMie->id,
                'name' => 'Indomie Rendang',
                'description' => 'Indomie goreng dengan bumbu rendang padang yang gurih dan sedap.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMie->id,
                'name' => 'Mie Sedap Goreng',
                'description' => 'Mie Sedaap goreng klasik dengan taburan kriuk bawang goreng spesial.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMie->id,
                'name' => 'Mie Sedap Ayam Bawang',
                'description' => 'Mie Sedaap kuah kaldu ayam bawang hangat yang nikmat disantap kapan saja.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMie->id,
                'name' => 'Mie Sedap Kari Ayam',
                'description' => 'Mie Sedaap kuah kental rasa kari ayam berempah yang menggugah selera.',
                'price' => 10000,
                'is_available' => true,
            ],
            [
                'category_id' => $catMie->id,
                'name' => 'Mie Sedap Soto',
                'description' => 'Mie Sedaap kuah soto segar lengkap dengan serbuk koya gurih.',
                'price' => 10000,
                'is_available' => true,
            ],

            // Add On (3 item)
            [
                'category_id' => $catAddOn->id,
                'name' => 'Telor',
                'description' => 'Tambahan telur ceplok atau rebus untuk melengkapi hidangan mie.',
                'price' => 5000,
                'is_available' => true,
            ],
            [
                'category_id' => $catAddOn->id,
                'name' => 'Sosis',
                'description' => 'Tambahan topping sosis goreng gurih.',
                'price' => 5000,
                'is_available' => true,
            ],
            [
                'category_id' => $catAddOn->id,
                'name' => 'Nugget',
                'description' => 'Tambahan topping nugget ayam krispi.',
                'price' => 5000,
                'is_available' => true,
            ],
        ];

        $menuMap = [];
        foreach ($menus as $m) {
            $slugPrefix = $m['category_id'] === $catAddOn->id ? 'add-on-' : '';
            $created = Menu::updateOrCreate(
                ['slug' => $slugPrefix.Str::slug($m['name'])],
                $m
            );
            $menuMap[$m['category_id'].'|'.$m['name']] = $created;
        }

        // 4. Figma mockup Orders
        // Order 1: DB-0124 (Paid via Midtrans QRIS, ready for cashier processing)
        $order1 = Order::updateOrCreate(
            ['order_code' => 'DB-0124'],
            [
                'table_number' => 'Meja 05',
                'customer_name' => 'Bima',
                'source' => 'QR Meja',
                'status' => 'menunggu',
                'payment_status' => 'paid',
                'payment_method' => 'qris',
                'midtrans_order_id' => 'DB-0124',
                'midtrans_transaction_status' => 'settlement',
                'amount_paid' => 42000,
                'change_amount' => 0,
                'paid_at' => Carbon::now()->subMinute(),
                'customer_notes' => 'Gula sedikit, karna perasaan bisa berubah tapi kopi harus tetap manis.',
                'total_amount' => 42000,
                'created_at' => Carbon::now()->subMinutes(2),
            ]
        );
        $order1->items()->delete();
        $order1->items()->createMany([
            [
                'menu_id' => null,
                'menu_name' => 'Kopi Susu Gula Aren',
                'price' => 16000,
                'quantity' => 2,
                'subtotal' => 32000,
            ],
            [
                'menu_id' => null,
                'menu_name' => 'Pisang Goreng',
                'price' => 10000,
                'quantity' => 1,
                'subtotal' => 10000,
            ],
        ]);

        // Order 2: DB-0123 (Unpaid - waiting Midtrans payment)
        $order2 = Order::updateOrCreate(
            ['order_code' => 'DB-0123'],
            [
                'table_number' => 'Meja 02',
                'customer_name' => 'Andi',
                'source' => 'QR Meja',
                'status' => 'menunggu',
                'payment_status' => 'unpaid',
                'midtrans_order_id' => 'DB-0123',
                'customer_notes' => 'Dingin, es batu dibanyakin ya kak.',
                'total_amount' => 25000,
                'created_at' => Carbon::now()->subMinutes(6),
            ]
        );
        $order2->items()->delete();
        $order2->items()->createMany([
            [
                'menu_id' => null,
                'menu_name' => 'Nasi Goreng Spesial',
                'price' => 25000,
                'quantity' => 1,
                'subtotal' => 25000,
            ],
        ]);

        // Order 3: DB-0122 (Unpaid - waiting Midtrans payment)
        $order3 = Order::updateOrCreate(
            ['order_code' => 'DB-0122'],
            [
                'table_number' => 'Meja 01',
                'customer_name' => 'Citra',
                'source' => 'QR Meja',
                'status' => 'menunggu',
                'payment_status' => 'unpaid',
                'midtrans_order_id' => 'DB-0122',
                'customer_notes' => 'Mienya jangan terlalu pedas ya mas.',
                'total_amount' => 61000,
                'created_at' => Carbon::now()->subMinutes(12),
            ]
        );
        $order3->items()->delete();
        $order3->items()->createMany([
            [
                'menu_id' => null,
                'menu_name' => 'Mie Nyemek De Barrio',
                'price' => 18000,
                'quantity' => 1,
                'subtotal' => 18000,
            ],
            [
                'menu_id' => ($menuMap[$catMinuman->id.'|Matcha'] ?? null)?->id,
                'menu_name' => 'Matcha Latte',
                'price' => 20000,
                'quantity' => 1,
                'subtotal' => 20000,
            ],
            [
                'menu_id' => null,
                'menu_name' => 'Ayam Geprek Sambal Korek',
                'price' => 23000,
                'quantity' => 1,
                'subtotal' => 23000,
            ],
        ]);

        // Order 4: DB-0121 (Diproses - paid via Midtrans)
        $order4 = Order::updateOrCreate(
            ['order_code' => 'DB-0121'],
            [
                'table_number' => 'Meja 04',
                'customer_name' => 'Fahri',
                'source' => 'Kasir',
                'status' => 'diproses',
                'payment_status' => 'paid',
                'payment_method' => 'qris',
                'midtrans_order_id' => 'DB-0121',
                'midtrans_transaction_status' => 'settlement',
                'amount_paid' => 37000,
                'change_amount' => 0,
                'paid_at' => Carbon::now()->subMinutes(21),
                'customer_notes' => 'Dibungkus terpisah ya.',
                'total_amount' => 37000,
                'created_at' => Carbon::now()->subMinutes(20),
            ]
        );
        $order4->items()->delete();
        $order4->items()->createMany([
            [
                'menu_id' => null,
                'menu_name' => 'Caramel Macchiato',
                'price' => 22000,
                'quantity' => 1,
                'subtotal' => 22000,
            ],
            [
                'menu_id' => null,
                'menu_name' => 'Roti Bakar Coklat Keju',
                'price' => 15000,
                'quantity' => 1,
                'subtotal' => 15000,
            ],
        ]);

        // Order 5: DB-0120 (Selesai - paid via Midtrans)
        $order5 = Order::updateOrCreate(
            ['order_code' => 'DB-0120'],
            [
                'table_number' => 'Meja 03',
                'customer_name' => 'Dina',
                'source' => 'QR Meja',
                'status' => 'selesai',
                'customer_notes' => 'Gula normal.',
                'total_amount' => 38000,
                'payment_status' => 'paid',
                'payment_method' => 'qris',
                'midtrans_order_id' => 'DB-0120',
                'midtrans_transaction_status' => 'settlement',
                'amount_paid' => 38000,
                'change_amount' => 0,
                'paid_at' => Carbon::now()->subMinutes(45),
                'created_at' => Carbon::now()->subMinutes(50),
            ]
        );
        $order5->items()->delete();
        $order5->items()->createMany([
            [
                'menu_id' => null,
                'menu_name' => 'Cafe Latte',
                'price' => 18000,
                'quantity' => 1,
                'subtotal' => 18000,
            ],
            [
                'menu_id' => null,
                'menu_name' => 'Chocolate Signature',
                'price' => 20000,
                'quantity' => 1,
                'subtotal' => 20000,
            ],
        ]);

        // 5. Offline Sync Queue (Matching Figma "K6 Sinkronisasi Offline")
        SyncQueue::truncate();

        // 18 records synced earlier today
        $baseDate = Carbon::today();
        for ($i = 1; $i <= 18; $i++) {
            $syncedMinute = 12 - (18 - $i); // up to 19:12
            $syncedHour = 19;
            if ($syncedMinute < 0) {
                $syncedHour = 18;
                $syncedMinute += 60;
            }
            $syncTime = $baseDate->copy()->setTime($syncedHour, max(0, $syncedMinute), 0);

            SyncQueue::create([
                'local_id' => 'sync-'.str_pad($i, 4, '0', STR_PAD_LEFT).'-'.substr(md5($i), 0, 8),
                'short_id' => substr(md5($i), 0, 4).'...'.substr(md5($i), -4),
                'total_amount' => 15000 + ($i * 2000),
                'description' => 'Tersinkron otomatis',
                'status' => 'synced',
                'attempts' => 1,
                'synced_at' => $syncTime,
                'created_at' => $syncTime->copy()->subMinutes(2),
                'updated_at' => $syncTime,
            ]);
        }

        // 3 active offline records shown in Figma screenshot
        // 1. a41f...c9e2 | 19.30 | Rp 27.000 | Menunggu koneksi | Pending
        SyncQueue::create([
            'local_id' => 'a41fc9e2-8172-4d51-9f20-9182a41fc9e2',
            'short_id' => 'a41f...c9e2',
            'order_payload' => [
                'table_number' => 'Meja 03',
                'customer_name' => 'Offline Order 1',
                'items' => [
                    ['name' => 'Americano', 'price' => 14000, 'quantity' => 1],
                    ['name' => 'Pisang Goreng', 'price' => 10000, 'quantity' => 1],
                ],
            ],
            'total_amount' => 27000,
            'description' => 'Menunggu koneksi',
            'status' => 'pending',
            'attempts' => 0,
            'created_at' => $baseDate->copy()->setTime(19, 30, 0),
            'updated_at' => $baseDate->copy()->setTime(19, 30, 0),
        ]);

        // 2. 7be0...12d8 | 19.34 | Rp 15.000 | Menunggu koneksi | Pending
        SyncQueue::create([
            'local_id' => '7be012d8-9921-4f10-b182-12717be012d8',
            'short_id' => '7be0...12d8',
            'order_payload' => [
                'table_number' => 'Meja 07',
                'customer_name' => 'Offline Order 2',
                'items' => [
                    ['name' => 'Roti Bakar Coklat Keju', 'price' => 15000, 'quantity' => 1],
                ],
            ],
            'total_amount' => 15000,
            'description' => 'Menunggu koneksi',
            'status' => 'pending',
            'attempts' => 0,
            'created_at' => $baseDate->copy()->setTime(19, 34, 0),
            'updated_at' => $baseDate->copy()->setTime(19, 34, 0),
        ]);

        // 3. c2d9...e771 | 19.36 | Rp 36.000 | Percobaan ke-3 | Gagal
        SyncQueue::create([
            'local_id' => 'c2d9e771-4412-4aa9-9912-3819c2d9e771',
            'short_id' => 'c2d9...e771',
            'order_payload' => [
                'table_number' => 'Meja 02',
                'customer_name' => 'Offline Order 3',
                'items' => [
                    ['name' => 'Mie Nyemek De Barrio', 'price' => 18000, 'quantity' => 2],
                ],
            ],
            'total_amount' => 36000,
            'description' => 'Percobaan ke-3',
            'status' => 'failed',
            'attempts' => 3,
            'last_attempt_at' => $baseDate->copy()->setTime(19, 37, 0),
            'created_at' => $baseDate->copy()->setTime(19, 36, 0),
            'updated_at' => $baseDate->copy()->setTime(19, 37, 0),
        ]);
    }
}
