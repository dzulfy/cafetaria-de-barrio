<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
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
            'name' => 'Goodday Freeze',
            'slug' => 'goodday-freeze',
            'description' => 'Kopi dingin menyegarkan',
            'price' => 9000,
            'is_available' => true,
        ]);

        $this->menuItem = MenuItem::create([
            'category_id' => $cat->id,
            'name' => 'Goodday Freeze',
            'description' => 'Kopi dingin menyegarkan',
            'price' => 9000,
            'is_available' => true,
        ]);
    }

    public function test_midtrans_config_uses_sandbox_endpoints_and_keys(): void
    {
        $service = app(MidtransService::class);

        $this->assertFalse($service->isProduction());
        $this->assertTrue($service->isConfigured());
        $this->assertSame('Mid-server-test-key', $service->serverKey());
        $this->assertSame('Mid-client-test-key', $service->clientKey());
        $this->assertSame('https://app.sandbox.midtrans.com/snap/snap.js', $service->snapJsUrl());
        $this->assertSame('https://app.sandbox.midtrans.com/snap/v1/transactions', $service->snapApiUrl());
        $this->assertSame('https://api.sandbox.midtrans.com/v2', $service->coreApiUrl());
    }

    public function test_customer_order_creates_midtrans_snap_token_and_shows_payment_prompt(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-qr-001',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-token-qr-001',
            ], 201),
        ]);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->menu->id,
            'action' => 'add',
            'quantity' => 2,
            'table_number' => '05',
        ])->assertStatus(200);

        $submitResponse = $this->post(route('orders.store'), [
            'table_number' => '05',
            'customer_name' => 'Siti',
            'notes' => 'Es sedikit',
        ]);

        $order = Order::firstOrFail();
        $submitResponse->assertRedirect(route('orders.status', ['order_code' => $order->order_code]));

        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('snap-token-qr-001', $order->snap_token);
        $this->assertSame($order->order_code, $order->midtrans_order_id);

        $statusResponse = $this->get(route('orders.status', ['order_code' => $order->order_code]));
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee('Menunggu Pembayaran');
        $statusResponse->assertSee('Bayar Sekarang via Midtrans');
        $statusResponse->assertSee('Cek Status Pembayaran');
    }

    public function test_kasir_cannot_process_unpaid_order_before_midtrans_payment_succeeds(): void
    {
        $order = Order::create([
            'order_code' => 'DB-9001',
            'table_number' => '05',
            'customer_name' => 'Rina',
            'subtotal' => 18000,
            'total_price' => 18000,
            'total_amount' => 18000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'midtrans_order_id' => 'DB-9001',
        ]);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.process', $order))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Pembayaran Midtrans belum berhasil. Pesanan baru dapat diproses setelah pembayaran lunas.',
            ]);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.complete', $order))
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertSame('menunggu', $order->fresh()->status);
    }

    public function test_midtrans_webhook_rejects_invalid_signature(): void
    {
        Order::create([
            'order_code' => 'DB-9002',
            'table_number' => '05',
            'total_amount' => 18000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'midtrans_order_id' => 'DB-9002',
        ]);

        $response = $this->postJson(route('midtrans.notification'), [
            'order_id' => 'DB-9002',
            'status_code' => '200',
            'gross_amount' => '18000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'invalid-signature',
        ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('orders', [
            'order_code' => 'DB-9002',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_midtrans_webhook_marks_order_paid_and_unlocks_kasir_processing_and_completion(): void
    {
        $order = Order::create([
            'order_code' => 'DB-9003',
            'table_number' => '05',
            'customer_name' => 'Andi',
            'subtotal' => 18000,
            'total_price' => 18000,
            'total_amount' => 18000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'midtrans_order_id' => 'DB-9003',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $this->menu->id,
            'menu_name' => 'Goodday Freeze',
            'price' => 9000,
            'quantity' => 2,
            'subtotal' => 18000,
        ]);

        $signature = hash('sha512', 'DB-9003'.'200'.'18000.00'.config('services.midtrans.server_key'));

        $webhookResponse = $this->postJson(route('midtrans.notification'), [
            'order_id' => 'DB-9003',
            'status_code' => '200',
            'gross_amount' => '18000.00',
            'payment_type' => 'qris',
            'transaction_id' => 'tx-midtrans-003',
            'transaction_status' => 'settlement',
            'signature_key' => $signature,
        ]);

        $webhookResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order_code' => 'DB-9003',
                'payment_status' => 'paid',
            ]);

        $fresh = $order->fresh();
        $this->assertSame('paid', $fresh->payment_status);
        $this->assertSame('qris', $fresh->payment_method);
        $this->assertSame(18000, $fresh->amount_paid);
        $this->assertSame('tx-midtrans-003', $fresh->midtrans_transaction_id);
        $this->assertNotNull($fresh->paid_at);

        // Now kasir can process and complete the paid order
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

    public function test_active_verify_endpoint_confirms_payment_via_midtrans_core_api_and_unlocks_kasir(): void
    {
        $order = Order::create([
            'order_code' => 'DB-9004',
            'table_number' => '05',
            'subtotal' => 9000,
            'total_price' => 9000,
            'total_amount' => 9000,
            'status' => 'menunggu',
            'payment_status' => 'pending',
            'midtrans_order_id' => 'DB-9004',
        ]);

        Http::fake([
            'https://api.sandbox.midtrans.com/v2/DB-9004/status' => Http::response([
                'order_id' => 'DB-9004',
                'status_code' => '200',
                'gross_amount' => '9000.00',
                'payment_type' => 'gopay',
                'transaction_id' => 'tx-midtrans-004',
                'transaction_status' => 'settlement',
            ], 200),
        ]);

        $verifyResponse = $this->postJson(route('midtrans.verify', ['order_code' => 'DB-9004']));
        $verifyResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'paid' => true,
                'payment_status' => 'paid',
            ]);

        $this->assertSame('gopay', $order->fresh()->payment_method);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.process', $order))
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_kasir_pos_order_uses_midtrans_snap_and_enforces_payment_before_process(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-pos-token-005',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-pos-token-005',
            ], 201),
            'https://api.sandbox.midtrans.com/v2/*/status' => Http::response([
                'status_code' => '200',
                'gross_amount' => '18000.00',
                'payment_type' => 'bank_transfer',
                'transaction_id' => 'tx-midtrans-pos-005',
                'transaction_status' => 'settlement',
            ], 200),
        ]);

        $createResponse = $this->actingAs($this->kasir)->postJson(route('orders.store'), [
            'table_number' => 'Meja 05',
            'customer_name' => 'Doni',
            'source' => 'Kasir',
            'items' => [
                ['menu_item_id' => $this->menuItem->id, 'quantity' => 2],
            ],
        ]);

        $createResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'snap_token' => 'snap-pos-token-005',
            ]);

        $order = Order::firstOrFail();
        $this->assertSame('unpaid', $order->payment_status);

        // Cannot process before payment confirmed
        $this->actingAs($this->kasir)
            ->postJson(route('orders.process', $order))
            ->assertStatus(422);

        // Verify payment then process succeeds
        $this->postJson(route('midtrans.verify', ['order_code' => $order->order_code]))
            ->assertStatus(200)
            ->assertJson(['paid' => true]);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.process', $order))
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_failed_or_expired_midtrans_payment_blocks_kasir_and_allows_regenerating_snap_token(): void
    {
        $order = Order::create([
            'order_code' => 'DB-9006',
            'table_number' => '05',
            'subtotal' => 9000,
            'total_price' => 9000,
            'total_amount' => 9000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'midtrans_order_id' => 'DB-9006',
            'snap_token' => 'old-expired-token',
        ]);

        $signature = hash('sha512', 'DB-9006'.'407'.'9000.00'.config('services.midtrans.server_key'));

        $this->postJson(route('midtrans.notification'), [
            'order_id' => 'DB-9006',
            'status_code' => '407',
            'gross_amount' => '9000.00',
            'transaction_status' => 'expire',
            'transaction_id' => 'tx-expired-006',
            'signature_key' => $signature,
        ])->assertStatus(200)->assertJson(['payment_status' => 'expired']);

        $this->actingAs($this->kasir)
            ->postJson(route('orders.process', $order))
            ->assertStatus(422);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'fresh-snap-token-006',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/fresh-snap-token-006',
            ], 201),
        ]);

        $tokenResponse = $this->postJson(route('midtrans.token', ['order_code' => 'DB-9006']), [
            'force_new' => true,
        ]);

        $tokenResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'snap_token' => 'fresh-snap-token-006',
            ]);

        $fresh = $order->fresh();
        $this->assertSame('unpaid', $fresh->payment_status);
        $this->assertNotSame('DB-9006', $fresh->midtrans_order_id);
        $this->assertStringStartsWith('DB-9006-', (string) $fresh->midtrans_order_id);
    }
}
