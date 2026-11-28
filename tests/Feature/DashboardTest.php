<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pengeluaran;
use App\Models\User;
use Database\Seeders\BahanSeeder;
use Database\Seeders\DashboardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'nama' => 'M. Fikri Firdaus',
            'email' => 'admin@debarrio.com',
            'peran' => 'admin',
        ]);
    }

    public function test_dashboard_page_can_be_rendered(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Pendapatan hari ini');
        $response->assertSee('Jumlah pesanan');
        $response->assertSee('Pengeluaran');
        $response->assertSee('Stok menipis');
        $response->assertSee('Pendapatan 7 hari terakhir');
        $response->assertSee('Menu terlaris');
        $response->assertSee('Pesanan terbaru');
    }

    public function test_dashboard_shows_zero_and_empty_states_when_database_is_empty(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Rp 0');
        $response->assertSee('Belum ada transaksi hari ini');
        $response->assertSee('0 menunggu diproses');
        $response->assertSee('0 transaksi');
        $response->assertSee('0 bahan');
        $response->assertSee('Semua stok aman');
        $response->assertSee('Belum ada data penjualan menu.');
        $response->assertSee('Belum ada pesanan masuk.');

        // Pastikan tidak ada angka dummy hardcoded yang muncul
        $response->assertDontSee('Rp 1.248.000');
        $response->assertDontSee('Rp 320.000');
        $response->assertDontSee('+12% dari kemarin');
        $response->assertDontSee('Kopi Susu Gula Aren');
    }

    public function test_dashboard_calculates_live_metrics_chart_trend_and_top_menus_from_database(): void
    {
        $table = CafeTable::create([
            'table_number' => '09',
            'name' => 'Meja 09 Patio',
            'token' => 't9Lm2Qx7Kp',
            'status' => 'aktif',
        ]);

        $category = Category::create([
            'name' => 'Kopi',
            'slug' => 'kopi',
            'icon' => 'coffee',
        ]);

        $bestMenu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Matcha Latte',
            'slug' => 'matcha-latte',
            'price' => 15000,
            'is_available' => true,
        ]);

        $secondMenu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Americano Dingin',
            'slug' => 'americano-dingin',
            'price' => 12000,
            'is_available' => true,
        ]);

        // Kemarin: Rp 100.000
        $yesterdayOrder = Order::factory()->create([
            'order_code' => 'DB-9001',
            'table_number' => 'Kasir',
            'status' => 'selesai',
            'payment_status' => 'paid',
            'subtotal' => 100000,
            'total_price' => 100000,
            'total_amount' => 100000,
        ]);
        $yesterdayOrder->timestamps = false;
        $yesterdayOrder->created_at = now()->subDay()->startOfDay()->addHours(12);
        $yesterdayOrder->updated_at = $yesterdayOrder->created_at;
        $yesterdayOrder->save();

        // 3 hari lalu: Rp 50.000
        $threeDaysAgoOrder = Order::factory()->create([
            'order_code' => 'DB-9002',
            'table_number' => 'Kasir',
            'status' => 'selesai',
            'payment_status' => 'paid',
            'subtotal' => 50000,
            'total_price' => 50000,
            'total_amount' => 50000,
        ]);
        $threeDaysAgoOrder->timestamps = false;
        $threeDaysAgoOrder->created_at = now()->subDays(3)->startOfDay()->addHours(15);
        $threeDaysAgoOrder->updated_at = $threeDaysAgoOrder->created_at;
        $threeDaysAgoOrder->save();

        // Hari ini aktif: Rp 90.000 + Rp 60.000 = Rp 150.000 (+50% dari kemarin)
        $todayOrder1 = Order::factory()->create([
            'order_code' => 'DB-9003',
            'table_id' => $table->id,
            'table_number' => '09',
            'status' => 'selesai',
            'payment_status' => 'paid',
            'subtotal' => 90000,
            'total_price' => 90000,
            'total_amount' => 90000,
        ]);

        $todayOrder2 = Order::factory()->create([
            'order_code' => 'DB-9004',
            'table_id' => $table->id,
            'table_number' => '09',
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'subtotal' => 60000,
            'total_price' => 60000,
            'total_amount' => 60000,
        ]);

        // Pesanan dibatalkan hari ini harus dikecualikan dari pendapatan, jumlah pesanan, & menu terlaris
        $cancelledOrder = Order::factory()->create([
            'order_code' => 'DB-9005',
            'table_id' => $table->id,
            'table_number' => '09',
            'status' => 'dibatalkan',
            'payment_status' => 'unpaid',
            'subtotal' => 500000,
            'total_price' => 500000,
            'total_amount' => 500000,
        ]);

        OrderItem::create([
            'order_id' => $todayOrder1->id,
            'menu_id' => $bestMenu->id,
            'menu_name' => $bestMenu->name,
            'price' => $bestMenu->price,
            'quantity' => 6,
            'subtotal' => 90000,
        ]);

        OrderItem::create([
            'order_id' => $todayOrder2->id,
            'menu_id' => $secondMenu->id,
            'menu_name' => $secondMenu->name,
            'price' => $secondMenu->price,
            'quantity' => 5,
            'subtotal' => 60000,
        ]);

        OrderItem::create([
            'order_id' => $cancelledOrder->id,
            'menu_id' => $secondMenu->id,
            'menu_name' => $secondMenu->name,
            'price' => $secondMenu->price,
            'quantity' => 99,
            'subtotal' => 1188000,
        ]);

        // Pengeluaran hari ini (Rp 45.000) & kemarin (tidak boleh terhitung di hari ini)
        Pengeluaran::create([
            'pengguna_id' => $this->admin->id,
            'deskripsi' => 'Es batu kristal',
            'jumlah' => 45000,
            'tanggal_pengeluaran' => now()->toDateString(),
        ]);

        Pengeluaran::create([
            'pengguna_id' => $this->admin->id,
            'deskripsi' => 'Galon kemarin',
            'jumlah' => 80000,
            'tanggal_pengeluaran' => now()->subDay()->toDateString(),
        ]);

        // Stok menipis vs aman
        Bahan::create([
            'nama' => 'Bubuk Matcha Premium',
            'satuan' => 'gram',
            'stok' => 100,
            'stok_minimum' => 500,
        ]);

        Bahan::create([
            'nama' => 'Air Mineral Galon',
            'satuan' => 'pcs',
            'stok' => 20,
            'stok_minimum' => 5,
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Rp 150.000');
        $response->assertSee('+50% dari kemarin');
        $response->assertSee('150 rb');
        $response->assertSee('100 rb');
        $response->assertSee('50 rb');
        $response->assertSee('1 menunggu diproses');
        $response->assertSee('Rp 45.000');
        $response->assertSee('1 transaksi');
        $response->assertSee('1 bahan');
        $response->assertSee('Perlu belanja');
        $response->assertSee('Bubuk Matcha Premium');
        $response->assertDontSee('Air Mineral Galon');
        $response->assertSee('Matcha Latte');
        $response->assertSee('6 terjual');
        $response->assertSee('Americano Dingin');
        $response->assertSee('5 terjual');
        $response->assertDontSee('99 terjual');
        $response->assertSee('DB-9004');
        $response->assertSee('Meja 09 Patio');
    }

    public function test_dashboard_displays_seeded_data_accurately(): void
    {
        $this->seed(BahanSeeder::class);
        $this->seed(DashboardSeeder::class);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Rp 1.248.000');
        $response->assertSee('+12% dari kemarin');
        $response->assertSee('47');
        $response->assertSee('8 menunggu diproses');
        $response->assertSee('Rp 320.000');
        $response->assertSee('3 transaksi');
        $response->assertSee('3 bahan');
        $response->assertSee('Perlu belanja');
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('86 terjual');
        $response->assertSee('DB-0124');
        $response->assertSee('Meja 05');
        $response->assertSee('Nescafe Sachet');
        $response->assertSee('Susu Kental Manis');
        $response->assertSee('Kentang Beku (French Fries)');
    }

    public function test_admin_dashboard_route_displays_database_metrics(): void
    {
        $this->seed(BahanSeeder::class);
        $this->seed(DashboardSeeder::class);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Rp 1.248.000');
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('86 terjual');
    }
}
