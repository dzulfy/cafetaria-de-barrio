<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
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
        $order = Order::where('order_code', 'DB-0124')->firstOrFail();

        $response = $this->postJson(route('orders.process', $order));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('diproses', $order->fresh()->status);
    }

    public function test_can_pay_order_and_calculate_change(): void
    {
        $order = Order::where('order_code', 'DB-0124')->firstOrFail();

        $response = $this->postJson(route('orders.pay', $order), [
            'payment_method' => 'tunai',
            'amount_paid' => 50000,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $fresh = $order->fresh();
        $this->assertEquals('selesai', $fresh->status);
        $this->assertEquals('tunai', $fresh->payment_method);
        $this->assertEquals(50000, $fresh->amount_paid);
        $this->assertEquals(8000, $fresh->change_amount); // 50000 - 42000 = 8000
    }

    public function test_can_create_new_order_from_qr_or_counter(): void
    {
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
        $response->assertSee('Warkop De Barrio');
    }
}
