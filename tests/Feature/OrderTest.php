<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed initial menu
        Menu::create([
            'name' => 'Kopi Susu Gula Aren',
            'slug' => 'kopi-susu-gula-aren',
            'description' => 'Espresso dengan susu dan aren',
            'price' => 16000,
            'category' => 'minuman',
            'icon' => 'coffee',
            'is_available' => true,
        ]);

        Menu::create([
            'name' => 'Pisang Goreng',
            'slug' => 'pisang-goreng',
            'description' => 'Pisang goreng renyah',
            'price' => 10000,
            'category' => 'makanan',
            'icon' => 'food',
            'is_available' => true,
        ]);
    }

    public function test_cart_page_renders_with_database_menu_items(): void
    {
        $response = $this->get(route('cart'));

        $response->assertStatus(200);
        $response->assertSee('Keranjang');
        $response->assertSee('Pesan mandiri');
        $response->assertSee('Meja 05');
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('Pisang Goreng');
        $response->assertSee('42.000');
        $response->assertSee('Kirim & Bayar via Midtrans', false);
    }

    public function test_can_update_cart_quantity(): void
    {
        $kopi = Menu::where('slug', 'kopi-susu-gula-aren')->first();

        // Initialize cart session
        $this->get(route('cart'));

        $response = $this->postJson(route('cart.update'), [
            'menu_id' => (string) $kopi->id,
            'action' => 'increase',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'totalItems' => 4, // 3 + 1
        ]);
    }

    public function test_can_submit_order_and_persists_to_database(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-test-order-01',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-test-order-01',
            ], 201),
        ]);

        // First visit cart to initialize items
        $this->get(route('cart'));

        $response = $this->post(route('orders.store'), [
            'table_number' => '05',
            'customer_name' => 'Rina',
            'notes' => 'gula sedikit',
        ]);

        $this->assertDatabaseHas('orders', [
            'table_number' => '05',
            'customer_name' => 'Rina',
            'notes' => 'gula sedikit',
            'total_price' => 42000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertCount(2, $order->items);

        $response->assertRedirect(route('orders.status', ['order_code' => $order->order_code]));
    }

    public function test_order_status_page_displays_order_details(): void
    {
        $order = Order::create([
            'order_code' => 'DB-0124',
            'table_number' => '05',
            'customer_name' => 'Rina',
            'notes' => 'gula sedikit',
            'subtotal' => 42000,
            'total_price' => 42000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_name' => 'Kopi Susu Gula Aren',
            'price' => 16000,
            'quantity' => 2,
            'subtotal' => 32000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_name' => 'Pisang Goreng',
            'price' => 10000,
            'quantity' => 1,
            'subtotal' => 10000,
        ]);

        $response = $this->get(route('orders.status', ['order_code' => 'DB-0124']));

        $response->assertStatus(200);
        $response->assertSee('Menunggu Pembayaran');
        $response->assertSee('DB-0124');
        $response->assertSee('Meja 05');
        $response->assertSee('Status pesanan');
        $response->assertSee('Menunggu');
        $response->assertSee('Ringkasan');
        $response->assertSee('2x Kopi Susu Gula Aren');
        $response->assertSee('1x Pisang Goreng');
        $response->assertSee('42.000');
        $response->assertSee('Bayar Sekarang via Midtrans');
        $response->assertSee('Pesan Lagi');
        $response->assertSee('Lihat Riwayat Pesanan');
    }

    public function test_can_check_and_update_order_status(): void
    {
        $order = Order::create([
            'order_code' => 'DB-0125',
            'table_number' => '05',
            'customer_name' => 'Budi',
            'total_price' => 16000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
        ]);

        // Check API status
        $statusResponse = $this->getJson(route('orders.check-status', ['order_code' => 'DB-0125']));
        $statusResponse->assertStatus(200);
        $statusResponse->assertJson([
            'order_code' => 'DB-0125',
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
        ]);

        // Unpaid order cannot move to diproses
        $this->postJson(route('orders.update-status', ['order_code' => 'DB-0125']), [
            'status' => 'diproses',
        ])->assertStatus(422);

        // Once paid via Midtrans, order can move to diproses
        $order->update(['payment_status' => 'paid', 'paid_at' => now()]);

        $updateResponse = $this->postJson(route('orders.update-status', ['order_code' => 'DB-0125']), [
            'status' => 'diproses',
        ]);
        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'order_code' => 'DB-0125',
            'status' => 'diproses',
        ]);
    }

    public function test_order_history_page_lists_orders(): void
    {
        Order::create([
            'order_code' => 'DB-0124',
            'table_number' => '05',
            'customer_name' => 'Rina',
            'total_price' => 42000,
            'status' => 'menunggu',
        ]);

        $response = $this->get(route('orders.history', ['table' => '05']));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Pesanan');
        $response->assertSee('DB-0124');
    }
}
