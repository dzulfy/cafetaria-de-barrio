<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $kasir = User::where('peran', 'kasir')->first() ?? User::factory()->create(['peran' => 'kasir']);
        $this->actingAs($kasir);
    }

    public function test_kasir_screen_loads_successfully_with_orders(): void
    {
        $response = $this->get(route('kasir.index'));

        $response->assertStatus(200);
        $response->assertSee('Pesanan Masuk');
        $response->assertSee('DB-0124');
        $response->assertSee('Meja 05');
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('Gula sedikit, karna perasaan bisa berubah tapi kopi harus tetap manis.');
    }

    public function test_can_retrieve_orders_as_json(): void
    {
        $response = $this->getJson(route('orders.list'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'orders',
            'counts' => ['menunggu', 'diproses', 'selesai'],
        ]);
    }

    public function test_can_process_waiting_order(): void
    {
        // DB-0124 is seeded as paid via Midtrans -> kasir can process
        $paidOrder = Order::where('order_code', 'DB-0124')->firstOrFail();

        $response = $this->postJson(route('orders.process', $paidOrder));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals('diproses', $paidOrder->fresh()->status);

        // DB-0123 is seeded as unpaid -> kasir cannot process yet
        $unpaidOrder = Order::where('order_code', 'DB-0123')->firstOrFail();

        $this->postJson(route('orders.process', $unpaidOrder))
            ->assertStatus(422)
            ->assertJson(['success' => false]);
        $this->assertEquals('menunggu', $unpaidOrder->fresh()->status);
    }

    public function test_can_pay_order_via_midtrans_and_complete_after_verification(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-kasir-0123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-kasir-0123',
            ], 201),
            'https://api.sandbox.midtrans.com/v2/DB-0123/status' => Http::response([
                'order_id' => 'DB-0123',
                'status_code' => '200',
                'gross_amount' => '25000.00',
                'payment_type' => 'qris',
                'transaction_id' => 'tx-kasir-0123',
                'transaction_status' => 'settlement',
            ], 200),
        ]);

        $order = Order::where('order_code', 'DB-0123')->firstOrFail();

        $payResponse = $this->postJson(route('orders.pay', $order));
        $payResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'snap_token' => 'snap-kasir-0123',
            ]);

        $this->postJson(route('midtrans.verify', ['order_code' => 'DB-0123']))
            ->assertStatus(200)
            ->assertJson(['paid' => true]);

        $this->postJson(route('orders.process', $order))
            ->assertStatus(200);

        $this->postJson(route('orders.complete', $order))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $fresh = $order->fresh();
        $this->assertEquals('selesai', $fresh->status);
        $this->assertEquals('paid', $fresh->payment_status);
        $this->assertEquals('qris', $fresh->payment_method);
        $this->assertEquals(25000, $fresh->amount_paid);
    }

    public function test_can_create_new_order_from_qr_or_counter(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-counter-01',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-counter-01',
            ], 201),
        ]);

        $menuItem = MenuItem::first();

        $response = $this->postJson(route('orders.store'), [
            'table_number' => 'Meja 06',
            'customer_name' => 'Doni',
            'customer_notes' => 'Tanpa sedotan plastik',
            'source' => 'QR Meja',
            'items' => [
                [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'table_number' => 'Meja 06',
            'customer_name' => 'Doni',
            'source' => 'QR Meja',
            'status' => 'menunggu',
            'total_amount' => $menuItem->price * 2,
        ]);
    }

    public function test_customer_qr_page_loads_successfully(): void
    {
        $response = $this->get(route('customer.qr', ['table' => 'Meja 05']));

        $response->assertStatus(200);
        $response->assertSee('Meja 05');
        $response->assertSee('Cafetaria De Barrio');
    }
}
