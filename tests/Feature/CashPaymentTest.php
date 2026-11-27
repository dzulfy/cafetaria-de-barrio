<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\GuestSession;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\GuestSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CashPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;

    private Menu $menu;

    private MenuItem $menuItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasir = User::factory()->create(['peran' => 'kasir']);

        CafeTable::create([
            'table_number' => '05',
            'name' => 'Meja 05',
            'token' => 'p8Gh3uJr1N',
            'status' => 'aktif',
        ]);

        $cat = Category::create([
            'name' => 'Minuman',
            'slug' => 'minuman',
            'icon' => 'coffee',
        ]);

        $this->menu = Menu::create([
            'category_id' => $cat->id,
            'name' => 'Matcha',
            'slug' => 'matcha',
            'description' => 'Green tea matcha creamy',
            'price' => 8000,
            'is_available' => true,
        ]);

        $this->menuItem = MenuItem::create([
            'category_id' => $cat->id,
            'name' => 'Matcha',
            'description' => 'Green tea matcha creamy',
            'price' => 8000,
            'is_available' => true,
        ]);
    }

    public function test_customer_can_choose_cash_payment_in_cart_and_sees_pay_at_cashier_instructions(): void
    {
        Http::fake();

        $cartResponse = $this->get(route('cart', ['table' => '05']));
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee('Bayar via QRIS / Transfer');
        $cartResponse->assertSee('Bayar di Kasir (Cash / Tunai)');

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->menu->id,
            'action' => 'add',
            'quantity' => 2,
            'table_number' => '05',
        ])->assertStatus(200);

        $submitResponse = $this->post(route('orders.store'), [
            'table_number' => '05',
            'customer_name' => 'Lina',
            'notes' => 'Es normal',
            'payment_method' => 'tunai',
        ]);

        $order = Order::firstOrFail();
        $submitResponse->assertRedirect(route('orders.status', ['order_code' => $order->order_code]));

        Http::assertNothingSent();

        $this->assertSame('tunai', $order->payment_method);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertNull($order->snap_token);

        $statusResponse = $this->get(route('orders.status', ['order_code' => $order->order_code]));
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee('Menunggu Pembayaran');
        $statusResponse->assertSee('Bayar di Kasir (Cash / Tunai)');
        $statusResponse->assertSee('Belum Dibayar • Bayar di Kasir');
        $statusResponse->assertSee('Ganti ke Bayar via QRIS / Transfer');
    }

    public function test_kasir_cannot_process_unpaid_cash_order_before_confirming_payment(): void
    {
        $order = Order::create([
            'order_code' => 'DB-8001',
            'table_number' => '05',
            'customer_name' => 'Lina',
            'subtotal' => 16000,
            'total_price' => 16000,
            'total_amount' => 16000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'payment_method' => 'tunai',
        ]);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.process', $order))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Pembayaran tunai di kasir belum dikonfirmasi. Pesanan baru dapat diproses setelah pembayaran lunas.',
            ]);

        $this->assertSame('menunggu', $order->fresh()->status);
    }

    public function test_kasir_can_confirm_cash_payment_calculate_change_and_sync_status_to_customer(): void
    {
        $order = Order::create([
            'order_code' => 'DB-8002',
            'table_number' => '05',
            'customer_name' => 'Budi',
            'subtotal' => 16000,
            'total_price' => 16000,
            'total_amount' => 16000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'payment_method' => 'tunai',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $this->menu->id,
            'menu_name' => 'Matcha',
            'price' => 8000,
            'quantity' => 2,
            'subtotal' => 16000,
        ]);

        $payResponse = $this->actingAs($this->kasir)
            ->postJson(route('orders.pay', $order), [
                'payment_method' => 'tunai',
                'amount_paid' => 50000,
            ]);

        $payResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'paid' => true,
                'payment_method' => 'tunai',
            ]);

        $fresh = $order->fresh();
        $this->assertSame('paid', $fresh->payment_status);
        $this->assertSame('tunai', $fresh->payment_method);
        $this->assertSame(50000, $fresh->amount_paid);
        $this->assertSame(34000, $fresh->change_amount);
        $this->assertNotNull($fresh->paid_at);

        // Customer status polling and status page immediately reflect paid cash payment
        $this->getJson(route('orders.check-status', ['order_code' => 'DB-8002']))
            ->assertStatus(200)
            ->assertJson([
                'order_code' => 'DB-8002',
                'status' => 'menunggu',
                'payment_status' => 'paid',
                'payment_method' => 'tunai',
            ]);

        $customerStatus = $this->get(route('orders.status', ['order_code' => 'DB-8002']));
        $customerStatus->assertStatus(200);
        $customerStatus->assertSee('Pembayaran Lunas (Tunai di Kasir)');
        $customerStatus->assertSee('34.000');

        // Cashier can now process and complete the order
        $this->actingAs($this->kasir)
            ->postJson(route('orders.process', $fresh))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSame('diproses', $order->fresh()->status);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.complete', $fresh))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSame('selesai', $order->fresh()->status);
    }

    public function test_kasir_cash_payment_rejects_insufficient_amount(): void
    {
        $order = Order::create([
            'order_code' => 'DB-8003',
            'table_number' => '05',
            'total_price' => 16000,
            'total_amount' => 16000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'payment_method' => 'tunai',
        ]);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.pay', $order), [
                'payment_method' => 'tunai',
                'amount_paid' => 10000,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['amount_paid']);

        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }

    public function test_kasir_pos_can_create_cash_order_and_confirm_payment(): void
    {
        Http::fake();

        $createResponse = $this->actingAs($this->kasir)->postJson(route('orders.store'), [
            'table_number' => 'Meja 05',
            'customer_name' => 'Tono',
            'source' => 'Kasir',
            'payment_method' => 'tunai',
            'items' => [
                ['menu_item_id' => $this->menuItem->id, 'quantity' => 2],
            ],
        ]);

        $createResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'snap_token' => null,
            ]);

        Http::assertNothingSent();

        $order = Order::firstOrFail();
        $this->assertSame('tunai', $order->payment_method);
        $this->assertSame('unpaid', $order->payment_status);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.pay', $order), [
                'payment_method' => 'tunai',
                'amount_paid' => 20000,
            ])
            ->assertStatus(200)
            ->assertJson(['paid' => true]);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.process', $order))
            ->assertStatus(200);
    }

    public function test_customer_cash_order_can_switch_to_midtrans_payment(): void
    {
        $guestSession = GuestSession::create();

        $order = Order::create([
            'order_code' => 'DB-8005',
            'guest_session_id' => $guestSession->id,
            'table_number' => '05',
            'subtotal' => 16000,
            'total_price' => 16000,
            'total_amount' => 16000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'payment_method' => 'tunai',
            'midtrans_order_id' => 'DB-8005',
        ]);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-switched-8005',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-switched-8005',
            ], 201),
        ]);

        $this->withSession([GuestSessionService::SESSION_KEY => $guestSession->id])
            ->postJson(route('midtrans.token', ['order_code' => 'DB-8005']))
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'snap_token' => 'snap-switched-8005',
            ]);
    }
}
