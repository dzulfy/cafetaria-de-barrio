<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Order;
use Database\Seeders\CategorySeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\WarkopDeBarrioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerQrFlowTest extends TestCase
{
    use RefreshDatabase;

    private CafeTable $tableOne;

    private CafeTable $tableTwo;

    private CafeTable $activeTable;

    private CafeTable $inactiveTable;

    private Menu $kopi;

    private Menu $v60;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tableOne = CafeTable::create([
            'table_number' => '01',
            'name' => 'Meja 01',
            'token' => 'x7Kp9aLm2Q',
            'status' => 'aktif',
        ]);

        $this->tableTwo = CafeTable::create([
            'table_number' => '02',
            'name' => 'Meja 02',
            'token' => 'b3Td8vNq5R',
            'status' => 'aktif',
        ]);

        $this->activeTable = CafeTable::create([
            'table_number' => '05',
            'name' => 'Meja 05',
            'token' => 'p8Gh3uJr1N',
            'status' => 'aktif',
        ]);

        $this->inactiveTable = CafeTable::create([
            'table_number' => '08',
            'name' => 'Meja 08',
            'token' => 'h6Rj2xFa8P',
            'status' => 'nonaktif',
        ]);

        $catKopi = Category::create([
            'name' => 'Kopi',
            'slug' => 'kopi',
        ]);

        $this->kopi = Menu::create([
            'category_id' => $catKopi->id,
            'name' => 'Kopi Susu Gula Aren',
            'slug' => 'kopi-susu-gula-aren',
            'description' => 'Espresso, susu, gula aren',
            'price' => 16000,
            'is_available' => true,
        ]);

        $this->v60 = Menu::create([
            'category_id' => $catKopi->id,
            'name' => 'V60 Manual Brew',
            'slug' => 'v60-manual-brew',
            'description' => 'Single origin, seduh manual',
            'price' => 18000,
            'is_available' => true,
        ]);
    }

    public function test_bare_landing_without_qr_does_not_default_to_meja_05_and_clears_stale_session(): void
    {
        // Simulate earlier session on Meja 05
        $this->get($this->activeTable->qr_url)->assertStatus(200);

        // Visiting bare '/' resets table context instead of defaulting to Meja 05
        $response = $this->get(route('landing'));

        $response->assertStatus(200);
        $response->assertSee('Scan QR di meja untuk memesan');
        $response->assertDontSee('Kamu memesan dari Meja 05');
        $response->assertSee('Meja 01');
        $this->assertNull(session('active_table'));
    }

    public function test_cafe_table_qr_url_resolves_to_customer_menu_with_table_and_token(): void
    {
        $expected = route('customer.menu', [
            'table' => '05',
            'token' => 'p8Gh3uJr1N',
        ]);

        $this->assertSame($expected, $this->activeTable->qr_url);
    }

    public function test_scanning_qr_opens_customer_menu_directly_for_the_table(): void
    {
        $response = $this->get($this->activeTable->qr_url);

        $response->assertStatus(200);
        $response->assertSee('Meja 05');
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('V60 Manual Brew');
        $response->assertSee(route('customer.menu.show', ['menu' => $this->kopi->id, 'table' => '05']), false);
    }

    public function test_carts_are_isolated_between_different_tables(): void
    {
        // Scan QR Meja 01 and add Kopi Susu Gula Aren
        $this->get($this->tableOne->qr_url)->assertSee('Meja 01');

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->kopi->id,
            'action' => 'increase',
            'table_number' => '01',
        ])->assertStatus(200)->assertJson(['totalItems' => 1]);

        // Switch to Meja 02 in same browser: cart must start empty
        $this->get($this->tableTwo->qr_url)->assertSee('Meja 02');
        $cartTwo = $this->get(route('cart', ['table' => '02']));
        $cartTwo->assertStatus(200);
        $cartTwo->assertSee('Pesan mandiri  |  Meja 02');
        $cartTwo->assertSee('Keranjang belanja masih kosong.');

        // Switching back to Meja 01 retains its item
        $cartOne = $this->get(route('cart', ['table' => '01']));
        $cartOne->assertStatus(200);
        $cartOne->assertSee('Pesan mandiri  |  Meja 01');
        $cartOne->assertSee('Kopi Susu Gula Aren');
    }

    public function test_short_qr_route_redirects_to_customer_menu_with_table(): void
    {
        $response = $this->get(route('customer.qr.scan', ['token' => $this->activeTable->token]));

        $response->assertRedirect(route('customer.menu', [
            'table' => $this->activeTable->table_number,
            'token' => $this->activeTable->token,
        ]));
    }

    public function test_scanning_inactive_table_qr_displays_warning_banner_and_blocks_ordering(): void
    {
        $response = $this->get($this->inactiveTable->qr_url);

        $response->assertStatus(200);
        $response->assertSee('Meja 08');
        $response->assertSee('Meja 08 sedang nonaktif. Silakan hubungi kasir.');

        // Block cart updates on inactive table
        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->kopi->id,
            'action' => 'increase',
            'table_number' => '08',
        ])->assertStatus(422)->assertJson(['success' => false]);

        // Block order submission on inactive table
        $this->post(route('orders.store'), [
            'table_number' => '08',
            'customer_name' => 'Rina',
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_end_to_end_qr_flow_from_landing_to_menu_cart_status_and_history(): void
    {
        // 1. Scan QR landing
        $this->get($this->activeTable->qr_url)->assertStatus(200);

        // 2. Open menu page
        $menuResponse = $this->get(route('customer.menu', ['table' => '05']));
        $menuResponse->assertStatus(200);
        $menuResponse->assertSee('Kopi Susu Gula Aren');

        // 3. Add item to cart from menu
        $addResponse = $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->kopi->id,
            'action' => 'increase',
            'table_number' => '05',
        ]);
        $addResponse->assertStatus(200);
        $addResponse->assertJson([
            'success' => true,
            'itemQuantity' => 1,
            'totalItems' => 1,
        ]);

        // 4. Open cart and see table + added item (without dummy pre-fill)
        $cartResponse = $this->get(route('cart', ['table' => '05']));
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee('Pesan mandiri  |  Meja 05');
        $cartResponse->assertSee('Kopi Susu Gula Aren');
        $cartResponse->assertSee('16.000');

        // 5. Submit order
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-qr-flow-01',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-qr-flow-01',
            ], 201),
        ]);

        $submitResponse = $this->post(route('orders.store'), [
            'table_number' => '05',
            'customer_name' => 'Rina',
            'notes' => 'gula sedikit',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $submitResponse->assertRedirect(route('orders.status', ['order_code' => $order->order_code]));

        // 6. Status page
        $statusResponse = $this->get(route('orders.status', ['order_code' => $order->order_code]));
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee('Menunggu Pembayaran');
        $statusResponse->assertSee('Bayar Sekarang via Midtrans');
        $statusResponse->assertSee($order->order_code);
        $statusResponse->assertSee('Meja 05');

        // 7. History page ("Pesanan Saya")
        $historyResponse = $this->get(route('orders.history', ['table' => '05']));
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee('Pesanan Saya');
        $historyResponse->assertSee($order->order_code);
        $historyResponse->assertSee('1x Kopi Susu Gula Aren');
    }

    public function test_landing_page_renders_official_logo_and_download_menu_feature(): void
    {
        $response = $this->get(route('landing'));

        $response->assertStatus(200);
        $response->assertSee('images/logo-de-barrio.jpg', false);
        $response->assertSee('images/daftar-menu-de-barrio.jpg', false);
        $response->assertSee(route('customer.menu.download'), false);
        $response->assertSee('Download Menu');
    }

    public function test_menu_poster_can_be_downloaded(): void
    {
        $response = $this->get(route('customer.menu.download'));

        $response->assertStatus(200);
        $response->assertDownload('Daftar-Menu-Cafeteria-De-Barrio.jpg');
    }

    public function test_real_menu_seeder_populates_exact_catalog_across_tables(): void
    {
        $this->seed([
            CategorySeeder::class,
            MenuSeeder::class,
            WarkopDeBarrioSeeder::class,
        ]);

        $this->assertSame(4, DB::table('kategori')->count());
        $this->assertSame(4, Category::count());
        $this->assertSame(29, Menu::count());
        $this->assertSame(29, MenuItem::count());

        $this->assertDatabaseHas('menus', ['name' => 'Nescafe', 'price' => 7000]);
        $this->assertDatabaseHas('menus', ['name' => 'Mix Platter', 'price' => 15000]);
        $this->assertDatabaseHas('menus', ['name' => 'Indomie Goreng', 'price' => 10000]);
        $this->assertDatabaseHas('menus', ['slug' => 'add-on-telor', 'name' => 'Telor', 'price' => 5000]);
        $this->assertDatabaseHas('menus', ['slug' => 'add-on-sosis', 'name' => 'Sosis', 'price' => 5000]);
        $this->assertDatabaseHas('menus', ['slug' => 'add-on-nugget', 'name' => 'Nugget', 'price' => 5000]);
        $this->assertDatabaseHas('menu_items', ['name' => 'Goodday Freeze', 'price' => 9000]);
    }

    public function test_customer_can_view_product_detail_page_for_each_menu(): void
    {
        $response = $this->get(route('customer.menu.show', [
            'menu' => $this->kopi->id,
            'table' => '05',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('Espresso, susu, gula aren');
        $response->assertSee('Rp 16.000');
        $response->assertSee('Kopi');
        $response->assertSee('Meja 05');
        $response->assertSee('V60 Manual Brew');
        $response->assertSee('Tambah ke Keranjang');
    }

    public function test_customer_can_add_custom_quantity_from_product_detail_to_cart(): void
    {
        $response = $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->kopi->id,
            'action' => 'add',
            'quantity' => 3,
            'table_number' => '05',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'itemQuantity' => 3,
            'totalItems' => 3,
        ]);

        $cartResponse = $this->get(route('cart', ['table' => '05']));
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee('Kopi Susu Gula Aren');
        $cartResponse->assertSee('48.000');
    }

    public function test_unavailable_menu_shows_habis_state_on_product_detail_page(): void
    {
        $soldOutMenu = Menu::create([
            'category_id' => $this->kopi->category_id,
            'name' => 'Matcha Spesial',
            'slug' => 'matcha-spesial',
            'description' => 'Matcha creamy terbatas',
            'price' => 15000,
            'is_available' => false,
        ]);

        $response = $this->get(route('customer.menu.show', ['menu' => $soldOutMenu->id]));

        $response->assertStatus(200);
        $response->assertSee('Matcha Spesial');
        $response->assertSee('Stok Habis');
    }
}
