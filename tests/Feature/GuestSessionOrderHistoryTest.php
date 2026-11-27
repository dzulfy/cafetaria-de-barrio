<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\GuestSession;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use App\Services\GuestSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class GuestSessionOrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    private CafeTable $tableOne;

    private CafeTable $tableTwo;

    private Menu $nasiGoreng;

    private Menu $mieAyam;

    private Menu $esTeh;

    private User $admin;

    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tableOne = CafeTable::create([
            'table_number' => '01',
            'name' => 'Meja 01',
            'token' => 'qrMeja01Tok',
            'status' => 'aktif',
        ]);

        $this->tableTwo = CafeTable::create([
            'table_number' => '02',
            'name' => 'Meja 02',
            'token' => 'qrMeja02Tok',
            'status' => 'aktif',
        ]);

        $category = Category::create([
            'name' => 'Makanan & Minuman',
            'slug' => 'makanan-minuman',
        ]);

        $this->nasiGoreng = Menu::create([
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'slug' => 'nasi-goreng',
            'description' => 'Nasi goreng spesial Cafetaria De Barrio',
            'price' => 15000,
            'is_available' => true,
        ]);

        $this->mieAyam = Menu::create([
            'category_id' => $category->id,
            'name' => 'Mie Ayam',
            'slug' => 'mie-ayam',
            'description' => 'Mie ayam hangat',
            'price' => 12000,
            'is_available' => true,
        ]);

        $this->esTeh = Menu::create([
            'category_id' => $category->id,
            'name' => 'Es Teh Manis',
            'slug' => 'es-teh-manis',
            'description' => 'Teh manis segar',
            'price' => 6000,
            'is_available' => true,
        ]);

        $this->admin = User::factory()->create(['peran' => 'admin']);
        $this->kasir = User::factory()->create(['peran' => 'kasir']);
    }

    public function test_two_devices_scanning_same_table_qr_get_distinct_persistent_guest_sessions(): void
    {
        // Device A scans QR Meja 01 via /meja/{token}
        $scanA = $this->get(route('customer.qr.scan', ['token' => $this->tableOne->token]));
        $scanA->assertRedirect(route('customer.menu', ['table' => '01', 'token' => $this->tableOne->token]));

        $guestA = session(GuestSessionService::SESSION_KEY);
        $this->assertIsString($guestA);
        $this->assertTrue(Str::isUuid($guestA));
        $this->assertDatabaseHas('guest_sessions', [
            'id' => $guestA,
            'table_id' => $this->tableOne->id,
        ]);

        // Refresh / navigate across pages keeps the exact same Guest Session for Device A
        $this->get(route('customer.menu', ['table' => '01']))->assertOk();
        $this->assertSame($guestA, session(GuestSessionService::SESSION_KEY));

        $this->get(route('landing'))->assertOk();
        $this->assertSame($guestA, session(GuestSessionService::SESSION_KEY));

        $this->get(route('orders.history'))->assertOk()->assertSee('Belum ada pesanan');
        $this->assertSame($guestA, session(GuestSessionService::SESSION_KEY));

        // Simulate a completely separate browser/device (Device B) scanning the SAME Meja 01 QR
        $this->flushSession();

        $this->get(route('customer.qr.scan', ['token' => $this->tableOne->token]))
            ->assertRedirect(route('customer.menu', ['table' => '01', 'token' => $this->tableOne->token]));

        $guestB = session(GuestSessionService::SESSION_KEY);
        $this->assertIsString($guestB);
        $this->assertTrue(Str::isUuid($guestB));
        $this->assertNotSame($guestA, $guestB);
        $this->assertDatabaseCount('guest_sessions', 2);
    }

    public function test_same_table_orders_from_different_devices_are_strictly_isolated_in_pesanan_saya(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-device-token',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-device-token',
            ], 201),
        ]);

        // 1. Device A scans QR Meja 01 and orders Nasi Goreng (2x = 30.000)
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestA = session(GuestSessionService::SESSION_KEY);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->nasiGoreng->id,
            'action' => 'add',
            'quantity' => 2,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'customer_name' => 'Perangkat A',
            'payment_method' => 'midtrans',
        ])->assertRedirect();

        $orderA = Order::where('guest_session_id', $guestA)->firstOrFail();
        $this->assertSame($this->tableOne->id, $orderA->table_id);

        // 2. Device B scans QR Meja 01 and orders Mie Ayam (1x = 12.000) using Cash
        $this->flushSession();
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestB = session(GuestSessionService::SESSION_KEY);
        $this->assertNotSame($guestA, $guestB);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->mieAyam->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'customer_name' => 'Perangkat B',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $orderB = Order::where('guest_session_id', $guestB)->firstOrFail();
        $this->assertSame($this->tableOne->id, $orderB->table_id);

        // 3. Device A opens Pesanan Saya -> only sees Nasi Goreng (NOT Mie Ayam)
        $this->flushSession();
        $historyA = $this->withSession([
            GuestSessionService::SESSION_KEY => $guestA,
            'active_table' => '01',
            'qr_flow' => true,
        ])->get(route('orders.history', ['table' => '01']));

        $historyA->assertOk();
        $historyA->assertSee('Pesanan Saya');
        $historyA->assertSee($orderA->order_code);
        $historyA->assertSee('2x Nasi Goreng');
        $historyA->assertSee('30.000');
        $historyA->assertSee('QRIS / Transfer');
        $historyA->assertSee('Belum Bayar');
        $historyA->assertSee('Menunggu');
        $historyA->assertDontSee($orderB->order_code);
        $historyA->assertDontSee('Mie Ayam');

        // 4. Device B opens Pesanan Saya -> only sees Mie Ayam (NOT Nasi Goreng)
        $this->flushSession();
        $historyB = $this->withSession([
            GuestSessionService::SESSION_KEY => $guestB,
            'active_table' => '01',
            'qr_flow' => true,
        ])->get(route('orders.history', ['table' => '01']));

        $historyB->assertOk();
        $historyB->assertSee($orderB->order_code);
        $historyB->assertSee('1x Mie Ayam');
        $historyB->assertSee('12.000');
        $historyB->assertSee('Cash / Tunai');
        $historyB->assertDontSee($orderA->order_code);
        $historyB->assertDontSee('Nasi Goreng');

        // 5. Device C (clean session) opens Pesanan Saya at Meja 01 -> sees empty state
        $this->flushSession();
        $historyC = $this->get(route('orders.history', ['table' => '01']));
        $historyC->assertOk();
        $historyC->assertSee('Belum ada pesanan');
        $historyC->assertDontSee($orderA->order_code);
        $historyC->assertDontSee($orderB->order_code);
    }

    public function test_multiple_orders_and_refresh_remain_in_same_guest_session_ordered_newest_first(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-multi-token',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-multi-token',
            ], 201),
        ]);

        // First scan & order
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestA = session(GuestSessionService::SESSION_KEY);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->nasiGoreng->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $firstOrder = Order::where('guest_session_id', $guestA)->firstOrFail();
        $firstOrder->update(['created_at' => now()->subMinutes(10)]);

        // Guest session still intact after completing submission
        $this->assertSame($guestA, session(GuestSessionService::SESSION_KEY));

        // Second order in same guest session
        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->esTeh->id,
            'action' => 'add',
            'quantity' => 2,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'midtrans',
        ])->assertRedirect();

        $secondOrder = Order::where('guest_session_id', $guestA)
            ->where('id', '!=', $firstOrder->id)
            ->firstOrFail();

        $this->assertSame($guestA, session(GuestSessionService::SESSION_KEY));

        // Open Pesanan Saya and refresh repeatedly -> both orders shown, newest first
        $history1 = $this->get(route('orders.history'));
        $history1->assertOk();
        $history1->assertSeeInOrder([$secondOrder->order_code, $firstOrder->order_code]);
        $history1->assertSee('2x Es Teh Manis');
        $history1->assertSee('1x Nasi Goreng');

        $historyRefresh = $this->get(route('orders.history'));
        $historyRefresh->assertOk();
        $historyRefresh->assertSeeInOrder([$secondOrder->order_code, $firstOrder->order_code]);
    }

    public function test_cross_device_order_detail_status_payment_and_query_param_spoofing_are_blocked(): void
    {
        $guestA = GuestSession::create(['table_id' => $this->tableOne->id]);
        $guestB = GuestSession::create(['table_id' => $this->tableOne->id]);

        $orderB = Order::create([
            'order_code' => 'DB-7702',
            'guest_session_id' => $guestB->id,
            'table_id' => $this->tableOne->id,
            'table_number' => '01',
            'customer_name' => 'Pemilik B',
            'subtotal' => 12000,
            'total_price' => 12000,
            'total_amount' => 12000,
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'payment_method' => 'midtrans',
            'midtrans_order_id' => 'DB-7702',
        ]);

        // Device A attempts to access Device B's order endpoints -> all denied (403)
        $this->withSession([GuestSessionService::SESSION_KEY => $guestA->id])
            ->get(route('orders.status', ['order_code' => 'DB-7702']))
            ->assertForbidden();

        $this->withSession([GuestSessionService::SESSION_KEY => $guestA->id])
            ->getJson(route('orders.check-status', ['order_code' => 'DB-7702']))
            ->assertForbidden();

        $this->withSession([GuestSessionService::SESSION_KEY => $guestA->id])
            ->getJson(route('orders.show', $orderB))
            ->assertForbidden();

        $this->withSession([GuestSessionService::SESSION_KEY => $guestA->id])
            ->postJson(route('midtrans.token', ['order_code' => 'DB-7702']))
            ->assertForbidden();

        $this->withSession([GuestSessionService::SESSION_KEY => $guestA->id])
            ->postJson(route('midtrans.verify', ['order_code' => 'DB-7702']))
            ->assertForbidden();

        // Request/URL parameter spoofing for guest_session_id must NEVER authorize Device A
        $this->withSession([GuestSessionService::SESSION_KEY => $guestA->id])
            ->get(route('orders.history', ['guest_session_id' => $guestB->id]))
            ->assertOk()
            ->assertSee('Belum ada pesanan')
            ->assertDontSee('DB-7702');

        $this->withSession([GuestSessionService::SESSION_KEY => $guestA->id])
            ->get(route('orders.status', ['order_code' => 'DB-7702', 'guest_session_id' => $guestB->id]))
            ->assertForbidden();

        // Device B CAN open its own order detail
        $this->flushSession();
        $this->withSession([GuestSessionService::SESSION_KEY => $guestB->id])
            ->get(route('orders.status', ['order_code' => 'DB-7702']))
            ->assertOk()
            ->assertSee('DB-7702');
    }

    public function test_scanning_another_table_keeps_guest_session_and_preserves_old_order_table(): void
    {
        // Device A starts at Meja 01 and creates first order
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestA = session(GuestSessionService::SESSION_KEY);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->nasiGoreng->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $firstOrder = Order::where('guest_session_id', $guestA)->firstOrFail();

        // Device A moves and scans QR Meja 02
        $this->get(route('customer.qr.scan', ['token' => $this->tableTwo->token]))
            ->assertRedirect(route('customer.menu', ['table' => '02', 'token' => $this->tableTwo->token]));

        // Guest session remains identical
        $this->assertSame($guestA, session(GuestSessionService::SESSION_KEY));

        // Second order is created at Meja 02
        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->mieAyam->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '02',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '02',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $secondOrder = Order::where('guest_session_id', $guestA)
            ->where('id', '!=', $firstOrder->id)
            ->firstOrFail();

        // Old order retains Meja 01; new order uses Meja 02
        $this->assertSame($this->tableOne->id, $firstOrder->fresh()->table_id);
        $this->assertSame('01', $firstOrder->fresh()->table_number);
        $this->assertSame($this->tableTwo->id, $secondOrder->table_id);
        $this->assertSame('02', $secondOrder->table_number);

        // Both orders remain in Device A's Pesanan Saya history
        $history = $this->get(route('orders.history'));
        $history->assertOk();
        $history->assertSee($firstOrder->order_code);
        $history->assertSee($secondOrder->order_code);
        $history->assertSee('Meja 01');
        $history->assertSee('Meja 02');
    }

    public function test_midtrans_qris_and_cash_payments_and_admin_visibility_all_work_together(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-qris-integration',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-qris-integration',
            ], 201),
        ]);

        // Device A places QRIS/Midtrans order on Meja 01
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestA = session(GuestSessionService::SESSION_KEY);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->nasiGoreng->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'midtrans',
        ])->assertRedirect();

        $qrisOrder = Order::where('guest_session_id', $guestA)->firstOrFail();

        // Device B places Cash order on Meja 01
        $this->flushSession();
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestB = session(GuestSessionService::SESSION_KEY);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->mieAyam->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $cashOrder = Order::where('guest_session_id', $guestB)->firstOrFail();

        // Midtrans webhook (no guest session, validated by SHA-512 signature) marks QRIS order paid
        $this->flushSession();
        $signature = hash('sha512', $qrisOrder->order_code.'200'.'15000.00'.config('services.midtrans.server_key'));
        $this->postJson(route('midtrans.notification'), [
            'order_id' => $qrisOrder->order_code,
            'status_code' => '200',
            'gross_amount' => '15000.00',
            'payment_type' => 'qris',
            'transaction_id' => 'tx-guest-qris-1',
            'transaction_status' => 'settlement',
            'signature_key' => $signature,
        ])->assertOk()->assertJson(['payment_status' => 'paid']);

        // Kasir confirms Cash order payment
        $this->actingAs($this->kasir)
            ->postJson(route('orders.pay', $cashOrder), [
                'payment_method' => 'tunai',
                'amount_paid' => 20000,
            ])
            ->assertOk()
            ->assertJson(['paid' => true]);

        // Device A sees its QRIS order paid in Pesanan Saya
        $this->flushSession();
        auth()->logout();
        $this->withSession([GuestSessionService::SESSION_KEY => $guestA])
            ->get(route('orders.history'))
            ->assertOk()
            ->assertSee($qrisOrder->order_code)
            ->assertSee('Lunas')
            ->assertDontSee($cashOrder->order_code);

        // Device B sees its Cash order paid in Pesanan Saya
        $this->flushSession();
        $this->withSession([GuestSessionService::SESSION_KEY => $guestB])
            ->get(route('orders.history'))
            ->assertOk()
            ->assertSee($cashOrder->order_code)
            ->assertSee('Lunas')
            ->assertDontSee($qrisOrder->order_code);

        // Admin retains full visibility across BOTH guest sessions
        $this->flushSession();
        $this->actingAs($this->admin)
            ->get(route('kasir.index'))
            ->assertOk()
            ->assertSee($qrisOrder->order_code)
            ->assertSee($cashOrder->order_code);

        $this->actingAs($this->admin)
            ->get(route('orders.status', ['order_code' => $qrisOrder->order_code]))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('orders.status', ['order_code' => $cashOrder->order_code]))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_registered_customer_can_access_their_qr_order_history_across_devices(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-registered-cust',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-registered-cust',
            ], 201),
        ]);

        $customerA = User::factory()->create([
            'nama' => 'Pelanggan A',
            'peran' => 'pelanggan',
        ]);

        // Device 1: Customer A logs in, scans QR Meja 01, orders Nasi Goreng
        $this->actingAs($customerA)
            ->get(route('customer.qr.scan', ['token' => $this->tableOne->token]))
            ->assertRedirect(route('customer.menu', ['table' => '01', 'token' => $this->tableOne->token]));

        // Authenticated customer must not rely on or spawn guest_session_id
        $this->assertNull(session(GuestSessionService::SESSION_KEY));

        $this->actingAs($customerA)->postJson(route('cart.update'), [
            'menu_id' => (string) $this->nasiGoreng->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->actingAs($customerA)->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'midtrans',
        ])->assertRedirect();

        $firstOrder = Order::where('user_id', $customerA->id)->firstOrFail();
        $firstOrder->update(['created_at' => now()->subMinutes(5)]);

        $this->assertNull($firstOrder->guest_session_id);
        $this->assertSame($this->tableOne->id, $firstOrder->table_id);
        $this->assertSame('Pelanggan A', $firstOrder->customer_name);

        // Customer A scans QR Meja 02 and orders Es Teh Manis
        $this->actingAs($customerA)
            ->get(route('customer.qr.scan', ['token' => $this->tableTwo->token]))
            ->assertRedirect(route('customer.menu', ['table' => '02', 'token' => $this->tableTwo->token]));

        $this->actingAs($customerA)->postJson(route('cart.update'), [
            'menu_id' => (string) $this->esTeh->id,
            'action' => 'add',
            'quantity' => 2,
            'table_number' => '02',
        ])->assertOk();

        $this->actingAs($customerA)->post(route('orders.store'), [
            'table_number' => '02',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $secondOrder = Order::where('user_id', $customerA->id)
            ->where('id', '!=', $firstOrder->id)
            ->firstOrFail();

        $this->assertNull($secondOrder->guest_session_id);
        $this->assertSame($this->tableTwo->id, $secondOrder->table_id);
        $this->assertSame($this->tableOne->id, $firstOrder->fresh()->table_id);

        // Simulate Device 2 (brand-new session/browser) logging in as Customer A
        $this->flushSession();

        $historyDevice2 = $this->actingAs($customerA)->get(route('orders.history'));
        $historyDevice2->assertOk();
        $historyDevice2->assertSee('Akun: Pelanggan A');
        $historyDevice2->assertSeeInOrder([$secondOrder->order_code, $firstOrder->order_code]);
        $historyDevice2->assertSee('2x Es Teh Manis');
        $historyDevice2->assertSee('1x Nasi Goreng');

        $this->actingAs($customerA)
            ->get(route('pelanggan.beranda'))
            ->assertOk()
            ->assertSeeInOrder([$secondOrder->order_code, $firstOrder->order_code]);

        // Customer A can access own order details and live status API from Device 2
        $this->actingAs($customerA)
            ->get(route('orders.status', ['order_code' => $firstOrder->order_code]))
            ->assertOk()
            ->assertSee($firstOrder->order_code);

        $this->actingAs($customerA)
            ->getJson(route('orders.check-status', ['order_code' => $secondOrder->order_code]))
            ->assertOk()
            ->assertJson(['order_code' => $secondOrder->order_code]);
    }

    public function test_strict_isolation_between_registered_customers_and_guests_on_same_table(): void
    {
        $customerA = User::factory()->create(['nama' => 'Customer A', 'peran' => 'pelanggan']);
        $customerB = User::factory()->create(['nama' => 'Customer B', 'peran' => 'pelanggan']);

        // 1. Guest places order at Meja 01
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestId = session(GuestSessionService::SESSION_KEY);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->nasiGoreng->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $guestOrder = Order::where('guest_session_id', $guestId)->firstOrFail();

        // 2. Customer A places order at Meja 01
        $this->flushSession();
        $this->actingAs($customerA)->postJson(route('cart.update'), [
            'menu_id' => (string) $this->mieAyam->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->actingAs($customerA)->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'midtrans',
        ])->assertRedirect();

        $orderA = Order::where('user_id', $customerA->id)->firstOrFail();

        // 3. Customer B places order at Meja 01
        $this->flushSession();
        $this->actingAs($customerB)->postJson(route('cart.update'), [
            'menu_id' => (string) $this->esTeh->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->actingAs($customerB)->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $orderB = Order::where('user_id', $customerB->id)->firstOrFail();

        // Customer A only sees Order A
        $this->flushSession();
        $this->actingAs($customerA)->get(route('orders.history', [
            'table' => '01',
            'user_id' => $customerB->id,
            'guest_session_id' => $guestId,
        ]))
            ->assertOk()
            ->assertSee($orderA->order_code)
            ->assertDontSee($orderB->order_code)
            ->assertDontSee($guestOrder->order_code);

        // Customer B cannot inspect Customer A's or Guest's order endpoints
        $this->flushSession();
        foreach ([$orderA, $guestOrder] as $foreignOrder) {
            $this->actingAs($customerB)
                ->get(route('orders.status', ['order_code' => $foreignOrder->order_code]))
                ->assertForbidden();

            $this->actingAs($customerB)
                ->getJson(route('orders.check-status', ['order_code' => $foreignOrder->order_code]))
                ->assertForbidden();

            $this->actingAs($customerB)
                ->getJson(route('orders.show', $foreignOrder))
                ->assertForbidden();

            $this->actingAs($customerB)
                ->postJson(route('midtrans.token', ['order_code' => $foreignOrder->order_code]))
                ->assertForbidden();

            $this->actingAs($customerB)
                ->postJson(route('midtrans.verify', ['order_code' => $foreignOrder->order_code]))
                ->assertForbidden();
        }

        // Guest cannot inspect Registered Customer A's order endpoints even when spoofing user_id
        $this->flushSession();
        auth()->logout();
        $this->withSession([GuestSessionService::SESSION_KEY => $guestId])
            ->get(route('orders.history', ['table' => '01', 'user_id' => $customerA->id]))
            ->assertOk()
            ->assertSee($guestOrder->order_code)
            ->assertDontSee($orderA->order_code)
            ->assertDontSee($orderB->order_code);

        $this->withSession([GuestSessionService::SESSION_KEY => $guestId])
            ->get(route('orders.status', ['order_code' => $orderA->order_code]))
            ->assertForbidden();

        $this->withSession([GuestSessionService::SESSION_KEY => $guestId])
            ->getJson(route('orders.check-status', ['order_code' => $orderA->order_code]))
            ->assertForbidden();

        $this->withSession([GuestSessionService::SESSION_KEY => $guestId])
            ->getJson(route('orders.show', $orderA))
            ->assertForbidden();

        $this->withSession([GuestSessionService::SESSION_KEY => $guestId])
            ->postJson(route('midtrans.token', ['order_code' => $orderA->order_code]))
            ->assertForbidden();

        $this->withSession([GuestSessionService::SESSION_KEY => $guestId])
            ->postJson(route('midtrans.verify', ['order_code' => $orderA->order_code]))
            ->assertForbidden();
    }

    public function test_guest_logging_in_does_not_auto_merge_guest_orders(): void
    {
        $customerA = User::factory()->create([
            'nama' => 'Pelanggan Login Baru',
            'peran' => 'pelanggan',
            'password' => 'secret123',
        ]);

        // Guest places an order before logging in
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestId = session(GuestSessionService::SESSION_KEY);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->nasiGoreng->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $guestOrder = Order::where('guest_session_id', $guestId)->firstOrFail();

        // Guest logs in through POST /login in the same browser session
        $this->post('/login', [
            'email' => $customerA->email,
            'password' => 'secret123',
        ])->assertRedirect(route('pelanggan.beranda'));

        // Guest order ownership must NOT be auto-merged into Customer A
        $freshGuestOrder = $guestOrder->fresh();
        $this->assertNull($freshGuestOrder->user_id);
        $this->assertSame($guestId, $freshGuestOrder->guest_session_id);

        // Customer A's Pesanan Saya remains isolated (empty) and cannot view Guest Order
        $this->get(route('orders.history'))
            ->assertOk()
            ->assertSee('Belum ada pesanan')
            ->assertDontSee($guestOrder->order_code);

        $this->get(route('pelanggan.beranda'))
            ->assertOk()
            ->assertSee('Belum ada pesanan')
            ->assertDontSee($guestOrder->order_code);

        $this->get(route('orders.status', ['order_code' => $guestOrder->order_code]))
            ->assertForbidden();
    }

    public function test_registered_customer_midtrans_qris_cash_and_admin_visibility_work_end_to_end(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-reg-qris',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-reg-qris',
            ], 201),
        ]);

        $customerA = User::factory()->create(['nama' => 'Pelanggan Midtrans', 'peran' => 'pelanggan']);

        // 1. Registered Customer A places QRIS order
        $this->actingAs($customerA)->postJson(route('cart.update'), [
            'menu_id' => (string) $this->nasiGoreng->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->actingAs($customerA)->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'midtrans',
        ])->assertRedirect();

        $qrisOrder = Order::where('user_id', $customerA->id)->firstOrFail();

        // 2. Registered Customer A places Cash order
        $this->actingAs($customerA)->postJson(route('cart.update'), [
            'menu_id' => (string) $this->mieAyam->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->actingAs($customerA)->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $cashOrder = Order::where('user_id', $customerA->id)
            ->where('id', '!=', $qrisOrder->id)
            ->firstOrFail();

        // 3. Guest places an order on the same table
        $this->flushSession();
        auth()->logout();
        $this->get($this->tableOne->qr_url)->assertOk();
        $guestId = session(GuestSessionService::SESSION_KEY);

        $this->postJson(route('cart.update'), [
            'menu_id' => (string) $this->esTeh->id,
            'action' => 'add',
            'quantity' => 1,
            'table_number' => '01',
        ])->assertOk();

        $this->post(route('orders.store'), [
            'table_number' => '01',
            'payment_method' => 'tunai',
        ])->assertRedirect();

        $guestOrder = Order::where('guest_session_id', $guestId)->firstOrFail();

        // 4. Midtrans webhook marks Customer A's QRIS order as paid
        $this->flushSession();
        $signature = hash('sha512', $qrisOrder->order_code.'200'.'15000.00'.config('services.midtrans.server_key'));
        $this->postJson(route('midtrans.notification'), [
            'order_id' => $qrisOrder->order_code,
            'status_code' => '200',
            'gross_amount' => '15000.00',
            'payment_type' => 'qris',
            'transaction_id' => 'tx-reg-qris-1',
            'transaction_status' => 'settlement',
            'signature_key' => $signature,
        ])->assertOk()->assertJson(['payment_status' => 'paid']);

        // 5. Kasir confirms Customer A's Cash order
        $this->actingAs($this->kasir)
            ->postJson(route('orders.pay', $cashOrder), [
                'payment_method' => 'tunai',
                'amount_paid' => 20000,
            ])
            ->assertOk()
            ->assertJson(['paid' => true]);

        // 6. Registered Customer A checks Pesanan Saya from Device 2 and sees both paid orders
        $this->flushSession();
        $this->actingAs($customerA)
            ->get(route('orders.history'))
            ->assertOk()
            ->assertSee($qrisOrder->order_code)
            ->assertSee($cashOrder->order_code)
            ->assertSee('Lunas')
            ->assertDontSee($guestOrder->order_code);

        // 7. Admin retains full visibility over Registered & Guest orders
        $this->flushSession();
        $this->actingAs($this->admin)
            ->get(route('kasir.index'))
            ->assertOk()
            ->assertSee($qrisOrder->order_code)
            ->assertSee($cashOrder->order_code)
            ->assertSee($guestOrder->order_code);

        $this->actingAs($this->admin)
            ->get(route('orders.status', ['order_code' => $qrisOrder->order_code]))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('orders.status', ['order_code' => $guestOrder->order_code]))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
}
