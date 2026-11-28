<?php

namespace Database\Seeders;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin?->id ?? 1;

        // 1. Pastikan Meja 01 s/d Meja 06 tersedia pada cafe_tables
        $defaultTokens = [
            '01' => 'x7Kp9aLm2Q',
            '02' => 'b3Td8vNq5R',
            '03' => 'm9Ws1cHy7E',
            '04' => 'k2Lf6zXp4A',
            '05' => 'p8Gh3uJr1N',
            '06' => 't5Yv7eBd9C',
        ];
        foreach ($defaultTokens as $num => $token) {
            CafeTable::firstOrCreate(
                ['table_number' => $num],
                [
                    'name' => 'Meja '.$num,
                    'token' => $token,
                    'status' => 'aktif',
                ]
            );
        }

        $meja05 = CafeTable::where('table_number', '05')->first();
        $meja02 = CafeTable::where('table_number', '02')->first();

        // 2. Pastikan kategori & menu dasar tersedia di categories / menus
        $catMinuman = Category::firstOrCreate(['slug' => 'minuman'], ['name' => 'Minuman', 'icon' => 'coffee']);
        $catSnack = Category::firstOrCreate(['slug' => 'snack'], ['name' => 'Snack', 'icon' => 'food']);

        $menuList = [
            ['name' => 'Kopi Susu Gula Aren', 'category_id' => $catMinuman->id, 'price' => 20000],
            ['name' => 'Es Kopi Susu', 'category_id' => $catMinuman->id, 'price' => 18000],
            ['name' => 'Pisang Goreng', 'category_id' => $catSnack->id, 'price' => 15000],
            ['name' => 'V60 Manual Brew', 'category_id' => $catMinuman->id, 'price' => 22000],
        ];

        $menusByName = [];
        foreach ($menuList as $m) {
            $menusByName[$m['name']] = Menu::firstOrCreate(
                ['slug' => Str::slug($m['name'])],
                [
                    'category_id' => $m['category_id'],
                    'name' => $m['name'],
                    'description' => 'Menu andalan Cafetaria De Barrio',
                    'price' => $m['price'],
                    'is_available' => true,
                ]
            );
        }

        // 3. Seed Pengeluaran hari ini (3 transaksi = Rp 320.000)
        $today = now()->toDateString();
        Pengeluaran::whereDate('tanggal_pengeluaran', $today)->delete();

        $pengeluarans = [
            ['deskripsi' => 'Beli cup take-away & sedotan', 'jumlah' => 150000],
            ['deskripsi' => 'Belanja sayur & bumbu dapur', 'jumlah' => 100000],
            ['deskripsi' => 'Beli gas elpiji 3kg & es batu', 'jumlah' => 70000],
        ];

        foreach ($pengeluarans as $p) {
            Pengeluaran::create([
                'pengguna_id' => $adminId,
                'deskripsi' => $p['deskripsi'],
                'jumlah' => $p['jumlah'],
                'tanggal_pengeluaran' => $today,
                'catatan' => 'Operasional harian',
            ]);
        }

        // 4. Seed Orders Hari Ini (Total 47 pesanan, 8 menunggu, total Rp 1.248.000)
        Order::whereDate('created_at', $today)->delete();
        $dayStart = now()->startOfDay();

        // Buat 36 pesanan selesai lampau terlebih dahulu:
        for ($i = 1; $i <= 36; $i++) {
            $ts = $dayStart->copy()->addMinutes($i);
            Order::create([
                'order_code' => sprintf('DB-0%03d', 70 + $i),
                'table_number' => 'Kasir',
                'customer_name' => 'Pelanggan Selesai '.$i,
                'source' => 'Kasir',
                'status' => 'selesai',
                'payment_status' => 'paid',
                'payment_method' => 'tunai',
                'subtotal' => 26000,
                'total_price' => 26000,
                'total_amount' => 26000,
                'created_at' => $ts,
                'updated_at' => $ts,
            ]);
        }

        $t107 = $dayStart->copy()->addMinutes(40);
        Order::create([
            'order_code' => 'DB-0107',
            'table_number' => 'Kasir',
            'customer_name' => 'Pelanggan Selesai 37',
            'source' => 'Kasir',
            'status' => 'selesai',
            'payment_status' => 'paid',
            'payment_method' => 'tunai',
            'subtotal' => 32000,
            'total_price' => 32000,
            'total_amount' => 32000,
            'created_at' => $t107,
            'updated_at' => $t107,
        ]);

        $t108 = $dayStart->copy()->addMinutes(45);
        Order::create([
            'order_code' => 'DB-0108',
            'table_number' => 'Kasir',
            'customer_name' => 'Pelanggan Selesai 38',
            'source' => 'Kasir',
            'status' => 'selesai',
            'payment_status' => 'paid',
            'payment_method' => 'tunai',
            'subtotal' => 30000,
            'total_price' => 30000,
            'total_amount' => 30000,
            'created_at' => $t108,
            'updated_at' => $t108,
        ]);

        // 6 pesanan menunggu sebelumnya (total status menunggu = 8):
        for ($i = 1; $i <= 6; $i++) {
            $ts = $dayStart->copy()->addMinutes(50 + $i);
            Order::create([
                'order_code' => sprintf('DB-0%03d', 114 + $i),
                'table_id' => $meja02?->id,
                'table_number' => '02',
                'customer_name' => 'Pelanggan Menunggu '.$i,
                'source' => 'QR Meja',
                'status' => 'menunggu',
                'payment_status' => 'unpaid',
                'subtotal' => 26000,
                'total_price' => 26000,
                'total_amount' => 26000,
                'created_at' => $ts,
                'updated_at' => $ts,
            ]);
        }

        // 3 Pesanan Terbaru yang tampil di card Pesanan Terbaru:
        $t121 = $dayStart->copy()->addMinutes(70);
        Order::create([
            'order_code' => 'DB-0121',
            'table_number' => 'Kasir',
            'customer_name' => 'Takeaway Kasir',
            'source' => 'Kasir',
            'status' => 'selesai',
            'payment_status' => 'paid',
            'payment_method' => 'tunai',
            'subtotal' => 24000,
            'total_price' => 24000,
            'total_amount' => 24000,
            'created_at' => $t121,
            'updated_at' => $t121,
        ]);

        $t123 = $dayStart->copy()->addMinutes(80);
        Order::create([
            'order_code' => 'DB-0123',
            'table_id' => $meja02?->id,
            'table_number' => '02',
            'customer_name' => 'Pelanggan Meja 2',
            'source' => 'QR Meja',
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'subtotal' => 28000,
            'total_price' => 28000,
            'total_amount' => 28000,
            'created_at' => $t123,
            'updated_at' => $t123,
        ]);

        $t124 = $dayStart->copy()->addMinutes(90);
        Order::create([
            'order_code' => 'DB-0124',
            'table_id' => $meja05?->id,
            'table_number' => '05',
            'customer_name' => 'Pelanggan Meja 5',
            'source' => 'QR Meja',
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'subtotal' => 42000,
            'total_price' => 42000,
            'total_amount' => 42000,
            'created_at' => $t124,
            'updated_at' => $t124,
        ]);

        // 5. Seed Historical Orders 6 hari sebelumnya (untuk grafik 7 hari, trend kemarin, & Menu Terlaris)
        Order::where('order_code', 'like', 'DB-HIST-%')->delete();

        $historicalDays = [
            6 => 820000,
            5 => 960000,
            4 => 1100000,
            3 => 740000,
            2 => 1320000,
            1 => 1114000, // kemarin: (1.248.000 - 1.114.000) / 1.114.000 = +12%
        ];

        $historicalOrders = [];
        foreach ($historicalDays as $daysAgo => $amount) {
            $histTs = now()->subDays($daysAgo)->startOfDay()->addHours(14);
            $histOrder = Order::create([
                'order_code' => 'DB-HIST-'.$daysAgo,
                'table_number' => 'Kasir',
                'customer_name' => 'Rekap Harian H-'.$daysAgo,
                'source' => 'Kasir',
                'status' => 'selesai',
                'payment_status' => 'paid',
                'payment_method' => 'tunai',
                'subtotal' => $amount,
                'total_price' => $amount,
                'total_amount' => $amount,
            ]);
            $histOrder->timestamps = false;
            $histOrder->created_at = $histTs;
            $histOrder->updated_at = $histTs;
            $histOrder->save();

            $historicalOrders[$daysAgo] = $histOrder;
        }

        // Seed relasi order_items untuk menghitung Menu Terlaris dari database
        $topSellingTargets = [
            ['order' => $historicalOrders[1], 'menu' => $menusByName['Kopi Susu Gula Aren'], 'qty' => 86],
            ['order' => $historicalOrders[2], 'menu' => $menusByName['Es Kopi Susu'], 'qty' => 64],
            ['order' => $historicalOrders[3], 'menu' => $menusByName['Pisang Goreng'], 'qty' => 48],
            ['order' => $historicalOrders[4], 'menu' => $menusByName['V60 Manual Brew'], 'qty' => 31],
        ];

        foreach ($topSellingTargets as $entry) {
            $menu = $entry['menu'];
            $qty = $entry['qty'];

            OrderItem::create([
                'order_id' => $entry['order']->id,
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
                'price' => $menu->price,
                'quantity' => $qty,
                'subtotal' => $menu->price * $qty,
            ]);
        }
    }
}
