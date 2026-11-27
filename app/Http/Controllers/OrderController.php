<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\SyncQueue;
use App\Services\GuestSessionService;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        public MidtransService $midtrans,
        public GuestSessionService $guestSessions,
    ) {}

    /**
     * Display the main cashier (tablet) view with real-time orders data.
     */
    public function index(Request $request): View|JsonResponse
    {
        $orders = Order::with('items.menuItem')
            ->orderBy('created_at', 'desc')
            ->get();

        $counts = [
            'menunggu' => $orders->where('status', 'menunggu')->count(),
            'diproses' => $orders->where('status', 'diproses')->count(),
            'selesai' => $orders->where('status', 'selesai')->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'orders' => $orders,
                'counts' => $counts,
            ]);
        }

        $categories = Category::with('menuItems')->get();
        $tables = RestaurantTable::orderBy('table_number')->get();

        $selectedOrder = $orders->firstWhere('order_code', 'DB-0124')
            ?? $orders->firstWhere('status', 'menunggu')
            ?? $orders->first();

        $syncQueue = SyncQueue::whereIn('status', ['pending', 'failed'])
            ->orderBy('created_at', 'asc')
            ->get();
        $menungguCount = SyncQueue::where('status', 'pending')->count();
        $gagalCount = SyncQueue::where('status', 'failed')->count();
        $syncedTodayCount = SyncQueue::where('status', 'synced')
            ->whereDate('synced_at', Carbon::today())
            ->count();
        $lastSyncedRecord = SyncQueue::where('status', 'synced')
            ->whereNotNull('synced_at')
            ->orderBy('synced_at', 'desc')
            ->first();
        $terakhirSinkron = $lastSyncedRecord ? $lastSyncedRecord->synced_at->format('H.i') : '19.12';

        $syncStats = [
            'menunggu' => $menungguCount,
            'gagal' => $gagalCount,
            'synced_today' => $syncedTodayCount,
            'terakhir_sinkron' => $terakhirSinkron,
            'total_menunggu' => $menungguCount + $gagalCount,
            'is_offline' => session()->get('is_offline', true),
        ];

        return view('kasir.index', compact('orders', 'counts', 'categories', 'tables', 'selectedOrder', 'syncQueue', 'syncStats'));
    }

    /**
     * Get a specific order details.
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        $this->guestSessions->authorizeOrder($request, $order);
        $order->load(['items.menuItem', 'cafeTable']);

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    /**
     * Update order status to 'diproses' (requires successful Midtrans payment).
     */
    public function process(Order $order): JsonResponse
    {
        if ($order->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dalam status menunggu.',
            ], 422);
        }

        if (! $order->isPaid()) {
            $message = $order->payment_method === 'tunai'
                ? 'Pembayaran tunai di kasir belum dikonfirmasi. Pesanan baru dapat diproses setelah pembayaran lunas.'
                : 'Pembayaran QRIS / Transfer belum berhasil. Pesanan baru dapat diproses setelah pembayaran lunas.';

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        $order->update(['status' => 'diproses']);

        return response()->json([
            'success' => true,
            'message' => "Pesanan {$order->order_code} sedang diproses.",
            'order' => $order->fresh(['items.menuItem']),
        ]);
    }

    /**
     * Mark a paid order as completed ('selesai').
     */
    public function complete(Order $order): JsonResponse
    {
        if (! $order->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran belum lunas. Pesanan belum dapat diselesaikan.',
            ], 422);
        }

        if (! in_array($order->status, ['diproses', 'menunggu'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat diselesaikan dari status saat ini.',
            ], 422);
        }

        $order->update(['status' => 'selesai']);

        return response()->json([
            'success' => true,
            'message' => "Pesanan {$order->order_code} selesai!",
            'order' => $order->fresh(['items.menuItem']),
        ]);
    }

    /**
     * Update order status to 'dibatalkan'.
     */
    public function reject(Order $order): JsonResponse
    {
        $order->update(['status' => 'dibatalkan']);

        return response()->json([
            'success' => true,
            'message' => "Pesanan {$order->order_code} berhasil ditolak.",
            'order' => $order->fresh(),
        ]);
    }

    /**
     * Confirm cash payment at cashier or initiate/resume Midtrans Snap payment.
     */
    public function pay(Request $request, Order $order): JsonResponse
    {
        if ($order->isPaid()) {
            $paidLabel = $order->payment_method === 'tunai' ? 'Tunai di Kasir' : 'QRIS / Transfer';

            return response()->json([
                'success' => true,
                'already_paid' => true,
                'message' => "Pesanan sudah dibayar melalui {$paidLabel}.",
                'order' => $order->fresh(['items.menuItem']),
            ]);
        }

        if ($order->status === 'dibatalkan') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan yang sudah dibatalkan tidak dapat dibayar.',
            ], 422);
        }

        $requestedMethod = strtolower((string) $request->input('payment_method', 'midtrans'));
        if ($requestedMethod === 'cash') {
            $requestedMethod = 'tunai';
        }

        if ($requestedMethod === 'tunai') {
            $totalAmount = (int) ($order->total_amount ?: $order->total_price ?: 0);

            $validated = $request->validate([
                'amount_paid' => ['required', 'numeric', 'min:'.$totalAmount],
            ]);

            $amountPaid = (int) $validated['amount_paid'];
            $changeAmount = $amountPaid - $totalAmount;

            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'tunai',
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'paid_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'paid' => true,
                'payment_method' => 'tunai',
                'message' => "Pembayaran tunai untuk {$order->order_code} berhasil dikonfirmasi! Pesanan sekarang dapat diproses.",
                'order' => $order->fresh(['items.menuItem']),
            ]);
        }

        try {
            $snap = $this->midtrans->createSnapTransaction($order, $request->boolean('force_new'));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'success' => true,
            'snap_token' => $snap['token'],
            'redirect_url' => $snap['redirect_url'],
            'client_key' => $this->midtrans->clientKey(),
            'message' => "Pembayaran QRIS / Transfer siap untuk {$order->order_code}.",
            'order' => $order->fresh(['items.menuItem']),
        ]);
    }

    /**
     * Create a new order (supports both Kasir/QR JSON API and QR-ordering session cart).
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->has('items')) {
            $validated = $request->validate([
                'table_number' => 'required|string',
                'customer_name' => 'nullable|string|max:100',
                'customer_notes' => 'nullable|string',
                'source' => 'nullable|string',
                'payment_method' => 'nullable|in:midtrans,tunai,cash',
                'items' => 'required|array|min:1',
                'items.*.menu_item_id' => 'required|exists:menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string',
            ]);

            $paymentMethod = in_array($validated['payment_method'] ?? null, ['tunai', 'cash'], true)
                ? 'tunai'
                : 'midtrans';

            $cleanTable = $this->normalizeTableNumber($validated['table_number']);
            $cafeTable = $cleanTable ? CafeTable::where('table_number', $cleanTable)->first() : null;
            $isCashierOrder = $this->guestSessions->isStaff($request) && ($validated['source'] ?? null) === 'Kasir';

            if (! $this->guestSessions->isStaff($request) && $cafeTable && $cafeTable->status !== 'aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Meja ini sedang nonaktif. Pemesanan tidak dapat diproses.',
                ], 422);
            }

            $owner = $this->guestSessions->resolveOrderOwner($request, $cafeTable, $isCashierOrder);
            $registeredCustomer = $this->guestSessions->registeredCustomer($request);

            $latestOrder = Order::orderBy('id', 'desc')->first();
            $nextNumber = 125;
            if ($latestOrder && preg_match('/DB-(\d+)/', $latestOrder->order_code, $matches)) {
                $nextNumber = max((int) $matches[1] + 1, $nextNumber);
            }
            $orderCode = sprintf('DB-%04d', $nextNumber);

            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $owner['user_id'],
                'guest_session_id' => $owner['guest_session_id'],
                'table_id' => $cafeTable?->id,
                'table_number' => $validated['table_number'],
                'customer_name' => ($validated['customer_name'] ?? null) ?: ($isCashierOrder ? 'Pelanggan' : ($registeredCustomer?->nama ?? 'Pelanggan')),
                'source' => $validated['source'] ?? 'QR Meja',
                'status' => 'menunggu',
                'payment_status' => 'unpaid',
                'payment_method' => $paymentMethod,
                'midtrans_order_id' => $orderCode,
                'customer_notes' => $validated['customer_notes'] ?? null,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($validated['items'] as $itemData) {
                $menu = MenuItem::findOrFail($itemData['menu_item_id']);
                $qty = (int) $itemData['quantity'];
                $subtotal = $menu->price * $qty;
                $total += $subtotal;

                $order->items()->create([
                    'menu_item_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $order->update(['total_amount' => $total, 'total_price' => $total, 'subtotal' => $total]);

            if ($paymentMethod === 'midtrans') {
                try {
                    $this->midtrans->createSnapTransaction($order);
                } catch (\Throwable $e) {
                    Log::warning('Midtrans Snap creation failed for POS order '.$order->order_code.': '.$e->getMessage());
                }
            }

            $freshOrder = $order->fresh(['items.menuItem']);
            $successMessage = $paymentMethod === 'tunai'
                ? "Pesanan baru {$orderCode} berhasil dibuat! Silakan konfirmasi pembayaran tunai di kasir."
                : "Pesanan baru {$orderCode} berhasil dibuat! Silakan lanjutkan pembayaran via QRIS atau Transfer.";

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'snap_token' => $freshOrder->snap_token,
                'snap_redirect_url' => $freshOrder->snap_redirect_url,
                'order' => $freshOrder,
            ], 201);
        }

        // Session cart submission (qr-ordering)
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:20'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'in:midtrans,tunai,cash'],
        ]);

        $paymentMethod = in_array($validated['payment_method'] ?? null, ['tunai', 'cash'], true)
            ? 'tunai'
            : 'midtrans';

        $cleanTable = $this->normalizeTableNumber($validated['table_number']);
        $cafeTable = $cleanTable ? CafeTable::where('table_number', $cleanTable)->first() : null;

        if ($cafeTable && $cafeTable->status !== 'aktif') {
            return redirect()->back()->with('error', 'Meja ini sedang nonaktif. Pemesanan tidak dapat diproses.');
        }

        $cartKey = $this->cartSessionKey($cleanTable);
        $cart = session()->get($cartKey);
        if (empty($cart)) {
            $cart = session()->get('cart', []);
        }

        if (empty($cart)) {
            return redirect()
                ->route('cart', $cleanTable ? ['table' => $cleanTable] : [])
                ->with('error', 'Keranjang belanja masih kosong.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $owner = $this->guestSessions->resolveOrderOwner($request, $cafeTable);
        $registeredCustomer = $this->guestSessions->registeredCustomer($request);
        $orderCode = Order::generateOrderCode();

        $order = Order::create([
            'order_code' => $orderCode,
            'user_id' => $owner['user_id'],
            'guest_session_id' => $owner['guest_session_id'],
            'table_id' => $cafeTable?->id,
            'table_number' => $cleanTable ?? $validated['table_number'],
            'customer_name' => ($validated['customer_name'] ?? null) ?: $registeredCustomer?->nama,
            'notes' => ($validated['notes'] ?? null) ?: null,
            'customer_notes' => ($validated['notes'] ?? null) ?: null,
            'subtotal' => $subtotal,
            'total_price' => $subtotal,
            'total_amount' => $subtotal,
            'source' => 'QR Meja',
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'payment_method' => $paymentMethod,
            'midtrans_order_id' => $orderCode,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'] ?? null,
                'menu_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        if ($paymentMethod === 'midtrans') {
            try {
                $this->midtrans->createSnapTransaction($order);
            } catch (\Throwable $e) {
                Log::warning('Midtrans Snap creation failed for QR order '.$order->order_code.': '.$e->getMessage());
            }
        }

        session()->put($cartKey, []);
        session()->put('cart', []);
        if ($cleanTable !== null) {
            session()->put('active_table', $cleanTable);
        }

        return redirect()->route('orders.status', ['order_code' => $order->order_code]);
    }

    private function normalizeTableNumber(?string $raw): ?string
    {
        $cleaned = trim((string) preg_replace('/^Meja\s+/i', '', (string) $raw));

        if ($cleaned === '') {
            return null;
        }

        if (ctype_digit($cleaned) && strlen($cleaned) === 1) {
            return str_pad($cleaned, 2, '0', STR_PAD_LEFT);
        }

        return $cleaned;
    }

    private function cartSessionKey(?string $tableNumber): string
    {
        return $tableNumber ? 'cart_table_'.$tableNumber : 'cart';
    }

    /**
     * Display transaction/order history.
     */
    public function history(Request $request): View|JsonResponse|RedirectResponse
    {
        if ($request->routeIs('kasir.riwayat') || ($request->wantsJson() && $this->guestSessions->isStaff($request))) {
            $query = Order::with(['items.menuItem', 'cafeTable'])
                ->whereIn('status', ['selesai', 'dibatalkan'])
                ->orderBy('updated_at', 'desc');

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s): void {
                    $q->where('order_code', 'like', "%{$s}%")
                        ->orWhere('table_number', 'like', "%{$s}%")
                        ->orWhere('customer_name', 'like', "%{$s}%");
                });
            }

            if ($request->filled('method') && $request->method !== 'semua') {
                $query->where('payment_method', $request->method);
            }

            $historyOrders = $query->paginate(15);

            if ($request->wantsJson()) {
                return response()->json($historyOrders);
            }

            return redirect()->route('kasir.index');
        }

        $tableNumber = $this->normalizeTableNumber($request->query('table') ?? session('active_table'));
        if ($tableNumber !== null) {
            session(['active_table' => $tableNumber]);
        }

        $activeStatus = (string) $request->query('status', 'semua');

        $ordersQuery = $this->guestSessions->scopeOrdersForRequester(
            $request,
            Order::with(['items', 'cafeTable'])->latest('created_at')->latest('id')
        );

        if (in_array($activeStatus, ['menunggu', 'diproses', 'selesai', 'dibatalkan'], true)) {
            $ordersQuery->where('status', $activeStatus);
        }

        $orders = $ordersQuery->get();

        if ($request->wantsJson()) {
            return response()->json([
                'orders' => $orders,
            ]);
        }

        return view('orders.history', [
            'orders' => $orders,
            'tableNumber' => $tableNumber,
            'activeStatus' => $activeStatus,
        ]);
    }

    /**
     * Customer self-service QR ordering page (renders unified Figma 01 Landing Page).
     */
    public function customerQr(Request $request): View
    {
        return app(CustomerMenuController::class)->landing($request);
    }

    /**
     * Display the cart screen (03 Keranjang).
     */
    public function cart(Request $request): View
    {
        $requestedTable = $this->normalizeTableNumber($request->query('table'));

        if ($requestedTable !== null) {
            $tableNumber = $requestedTable;
            session(['active_table' => $tableNumber]);
        } elseif (session()->has('active_table')) {
            $tableNumber = $this->normalizeTableNumber((string) session('active_table'));
        } elseif (! session()->get('qr_flow', false)) {
            $tableNumber = '05';
            session(['active_table' => '05']);
        } else {
            $tableNumber = null;
        }

        $cafeTable = $tableNumber ? CafeTable::where('table_number', $tableNumber)->first() : null;
        $isTableInactive = $cafeTable !== null && $cafeTable->status !== 'aktif';

        $cartKey = $this->cartSessionKey($tableNumber);
        $cart = session()->get($cartKey);

        if ($cart === null && ! session()->get('qr_flow', false)) {
            $kopi = Menu::where('slug', 'kopi-susu-gula-aren')->first();
            $pisang = Menu::where('slug', 'pisang-goreng')->first();

            $cart = [];
            if ($kopi) {
                $cart[(string) $kopi->id] = [
                    'id' => $kopi->id,
                    'name' => $kopi->name,
                    'price' => $kopi->price,
                    'icon' => $kopi->icon,
                    'image' => $kopi->image,
                    'quantity' => 2,
                ];
            }
            if ($pisang) {
                $cart[(string) $pisang->id] = [
                    'id' => $pisang->id,
                    'name' => $pisang->name,
                    'price' => $pisang->price,
                    'icon' => $pisang->icon,
                    'image' => $pisang->image,
                    'quantity' => 1,
                ];
            }
            session()->put($cartKey, $cart);
        }

        $cart = $cart ?? [];

        $totalItems = array_sum(array_column($cart, 'quantity'));
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $activeTables = CafeTable::where('status', 'aktif')->orderBy('table_number')->get();

        return view('orders.cart', [
            'cart' => $cart,
            'tableNumber' => $tableNumber,
            'isTableInactive' => $isTableInactive,
            'activeTables' => $activeTables,
            'totalItems' => $totalItems,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Update quantity of an item in cart via AJAX or POST.
     */
    public function updateCart(Request $request): JsonResponse|RedirectResponse
    {
        $menuId = (string) $request->input('menu_id');
        $action = (string) $request->input('action');
        $quantity = max(1, min(99, (int) $request->input('quantity', 1)));
        $tableNumber = $this->normalizeTableNumber($request->input('table_number') ?? session('active_table'));

        if ($tableNumber !== null) {
            $cafeTable = CafeTable::where('table_number', $tableNumber)->first();
            if ($cafeTable && $cafeTable->status !== 'aktif') {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Meja ini sedang nonaktif. Silakan hubungi kasir.',
                    ], 422);
                }

                return redirect()->back()->with('error', 'Meja ini sedang nonaktif. Silakan hubungi kasir.');
            }
        }

        $cartKey = $this->cartSessionKey($tableNumber);
        $cart = session()->get($cartKey, []);

        if (($action === 'increase' || $action === 'add') && ! isset($cart[$menuId])) {
            $menu = Menu::find($menuId);
            if ($menu && $menu->is_available) {
                $cart[$menuId] = [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'price' => (int) $menu->price,
                    'icon' => $menu->icon ?? 'coffee',
                    'image' => $menu->image,
                    'quantity' => $quantity,
                ];
            }
        } elseif (isset($cart[$menuId])) {
            if ($action === 'increase' || $action === 'add') {
                $cart[$menuId]['quantity'] += $quantity;
            } elseif ($action === 'decrease') {
                $cart[$menuId]['quantity']--;
                if ($cart[$menuId]['quantity'] <= 0) {
                    unset($cart[$menuId]);
                }
            } elseif ($action === 'remove') {
                unset($cart[$menuId]);
            }
        }

        session()->put($cartKey, $cart);

        $totalItems = array_sum(array_column($cart, 'quantity'));
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $cart,
                'itemQuantity' => $cart[$menuId]['quantity'] ?? 0,
                'totalItems' => $totalItems,
                'subtotal' => $subtotal,
                'formattedSubtotal' => 'Rp '.number_format($subtotal, 0, ',', '.'),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Display order status screen (04 Status Pesanan).
     */
    public function status(Request $request, string $order_code): View
    {
        $order = Order::with(['items', 'cafeTable'])->where('order_code', $order_code)->firstOrFail();
        $this->guestSessions->authorizeOrder($request, $order);

        return view('orders.status', [
            'order' => $order,
            'midtransClientKey' => $this->midtrans->clientKey(),
            'midtransSnapJsUrl' => $this->midtrans->snapJsUrl(),
            'midtransIsProduction' => $this->midtrans->isProduction(),
        ]);
    }

    /**
     * Live status check API.
     */
    public function checkStatus(Request $request, string $order_code): JsonResponse
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();
        $this->guestSessions->authorizeOrder($request, $order);

        return response()->json([
            'order_code' => $order->order_code,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'midtrans_transaction_status' => $order->midtrans_transaction_status,
            'paid_at' => $order->paid_at?->toIso8601String(),
        ]);
    }

    /**
     * Advance order status (requires successful Midtrans payment for processing/completion).
     */
    public function updateStatus(Request $request, string $order_code): JsonResponse|RedirectResponse
    {
        if (! $this->guestSessions->isStaff($request)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah status pesanan.');
        }

        $order = Order::where('order_code', $order_code)->firstOrFail();
        $newStatus = (string) $request->input('status');

        if (in_array($newStatus, ['diproses', 'selesai'], true) && ! $order->isPaid()) {
            $message = 'Pembayaran belum lunas. Pesanan baru dapat diproses setelah pembayaran lunas.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        if (in_array($newStatus, ['menunggu', 'diproses', 'selesai', 'dibatalkan'], true)) {
            $order->update(['status' => $newStatus]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
            ]);
        }

        return redirect()->back();
    }
}
