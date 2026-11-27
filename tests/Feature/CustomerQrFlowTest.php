<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerQrFlowTest extends TestCase
{
    use RefreshDatabase;

    private CafeTable $activeTable;

    private CafeTable $inactiveTable;

    private Menu $kopi;

    private Menu $v60;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function test_cafe_table_qr_url_resolves_to_customer_landing_with_table_and_token(): void
    {
        $expected = route('landing', [
            'table' => '05',
            'token' => 'p8Gh3uJr1N',
        ]);

        $this->assertSame($expected, $this->activeTable->qr_url);
    }

    public function test_scanning_qr_opens_figma_customer_landing_for_the_table(): void
    {
        $response = $this->get($this->activeTable->qr_url);

        $response->assertStatus(200);
        $response->assertSee('Kamu memesan dari Meja 05');
        $response->assertSee('Ngopi manual brew,');
        $response->assertSee('harga anak kos.');
        $response->assertSee('Lihat Menu');
        $response->assertSee('Keranjang');
        $response->assertSee('Menu Favorit');
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('V60 Manual Brew');
        $response->assertSee('Tentang Kami');
        $response->assertSee('Lokasi');
    }

    public function test_short_qr_route_redirects_to_customer_landing_with_table(): void
    {
        $response = $this->get(route('customer.qr.scan', ['token' => $this->activeTable->token]));

        $response->assertRedirect(route('landing', [
            'table' => $this->activeTable->table_number,
            'token' => $this->activeTable->token,
        ]));
    }

    public function test_scanning_inactive_table_qr_displays_warning_banner(): void
    {
        $response = $this->get($this->inactiveTable->qr_url);

        $response->assertStatus(200);
        $response->assertSee('Kamu memesan dari Meja 08');
        $response->assertSee('Meja ini sedang nonaktif. Silakan hubungi kasir.');
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
        $statusResponse->assertSee('Pesanan terkirim!');
        $statusResponse->assertSee($order->order_code);
        $statusResponse->assertSee('Meja 05');

        // 7. History page ("Pesanan Saya")
        $historyResponse = $this->get(route('orders.history', ['table' => '05']));
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee('Pesanan Saya');
        $historyResponse->assertSee($order->order_code);
        $historyResponse->assertSee('1x Kopi Susu Gula Aren');
    }
}
