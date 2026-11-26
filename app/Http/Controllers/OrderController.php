<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\SyncQueue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
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
    public function show(Order $order): JsonResponse
    {
        $order->load('items.menuItem');

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    /**
     * Update order status to 'diproses'.
     */
    public function process(Order $order): JsonResponse
    {
        if ($order->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dalam status menunggu.',
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
     * Process payment for an order.
     */
    public function pay(Request $request, Order $order): JsonResponse
    {
        $totalAmount = $order->total_amount ?? $order->total_price ?? 0;

        $validated = $request->validate([
            'payment_method' => 'required|in:tunai,qris,transfer',
            'amount_paid' => 'required|numeric|min:'.$totalAmount,
        ]);

        $amountPaid = (int) $validated['amount_paid'];
        $changeAmount = $amountPaid - $totalAmount;

        $order->update([
            'status' => 'selesai',
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'amount_paid' => $amountPaid,
            'change_amount' => $changeAmount,
            'paid_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pembayaran {$order->order_code} berhasil!",
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
                'items' => 'required|array|min:1',
                'items.*.menu_item_id' => 'required|exists:menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string',
            ]);

            $latestOrder = Order::orderBy('id', 'desc')->first();
            $nextNumber = 125;
            if ($latestOrder && preg_match('/DB-(\d+)/', $latestOrder->order_code, $matches)) {
                $nextNumber = max((int) $matches[1] + 1, $nextNumber);
            }
            $orderCode = sprintf('DB-%04d', $nextNumber);

            $order = Order::create([
                'order_code' => $orderCode,
                'table_number' => $validated['table_number'],
                'customer_name' => $validated['customer_name'] ?? 'Pelanggan',
                'source' => $validated['source'] ?? 'QR Meja',
                'status' => 'menunggu',
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

            return response()->json([
                'success' => true,
                'message' => "Pesanan baru {$orderCode} berhasil dibuat!",
                'order' => $order->fresh(['items.menuItem']),
            ], 201);
        }

        // Session cart submission (qr-ordering)
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:20'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja masih kosong.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'table_number' => $validated['table_number'] ?? '05',
            'customer_name' => $validated['customer_name'] ?: null,
            'notes' => $validated['notes'] ?: null,
            'customer_notes' => $validated['notes'] ?: null,
            'subtotal' => $subtotal,
            'total_price' => $subtotal,
            'total_amount' => $subtotal,
            'source' => 'QR Meja',
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
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

        session()->forget('cart');

        return redirect()->route('orders.status', ['order_code' => $order->order_code]);
    }

    /**
     * Display transaction/order history.
     */
    public function history(Request $request): View|JsonResponse
    {
        if ($request->routeIs('orders.history')) {
            $tableNumber = (string) $request->query('table', '05');
            $orders = Order::with('items')
                ->where('table_number', $tableNumber)
                ->latest()
                ->get();

            return view('orders.history', [
                'orders' => $orders,
                'tableNumber' => $tableNumber,
            ]);
        }

        $query = Order::with('items.menuItem')
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->orderBy('updated_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
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

        // Return customer order history view if customer route or table provided
        if ($request->filled('table') || ! $request->is('kasir*')) {
            $tableNumber = (string) $request->query('table', '05');
            $orders = Order::with('items')
                ->when($request->filled('table'), function ($q) use ($tableNumber) {
                    $q->where('table_number', $tableNumber);
                })
                ->latest()
                ->get();

            return view('orders.history', [
                'orders' => $orders,
                'tableNumber' => $tableNumber,
            ]);
        }

        return redirect()->route('kasir.index');
    }

    /**
     * Customer self-service QR ordering page.
     */
    public function customerQr(Request $request): View
    {
        $tableNumber = $request->query('table', 'Meja 05');
        $categories = Category::with(['menuItems' => function ($q) {
            $q->where('is_available', true);
        }])->get();

        return view('customer.order', compact('tableNumber', 'categories'));
    }

    /**
     * Display the cart screen (03 Keranjang).
     */
    public function cart(Request $request): View
    {
        $tableNumber = (string) $request->query('table', '05');
        $cart = session()->get('cart');

        if (empty($cart)) {
            $kopi = Menu::where('slug', 'kopi-susu-gula-aren')->first();
            $pisang = Menu::where('slug', 'pisang-goreng')->first();

            $cart = [];
            if ($kopi) {
                $cart[(string) $kopi->id] = [
                    'id' => $kopi->id,
                    'name' => $kopi->name,
                    'price' => $kopi->price,
                    'icon' => $kopi->icon,
                    'quantity' => 2,
                ];
            }
            if ($pisang) {
                $cart[(string) $pisang->id] = [
                    'id' => $pisang->id,
                    'name' => $pisang->name,
                    'price' => $pisang->price,
                    'icon' => $pisang->icon,
                    'quantity' => 1,
                ];
            }
            session()->put('cart', $cart);
        }

        $totalItems = array_sum(array_column($cart, 'quantity'));
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return view('orders.cart', [
            'cart' => $cart,
            'tableNumber' => $tableNumber,
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

        $cart = session()->get('cart', []);

        if (isset($cart[$menuId])) {
            if ($action === 'increase') {
                $cart[$menuId]['quantity']++;
            } elseif ($action === 'decrease') {
                $cart[$menuId]['quantity']--;
                if ($cart[$menuId]['quantity'] <= 0) {
                    unset($cart[$menuId]);
                }
            } elseif ($action === 'remove') {
                unset($cart[$menuId]);
            }
        }

        session()->put('cart', $cart);

        $totalItems = array_sum(array_column($cart, 'quantity'));
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $cart,
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
    public function status(string $order_code): View
    {
        $order = Order::with('items')->where('order_code', $order_code)->firstOrFail();

        return view('orders.status', [
            'order' => $order,
        ]);
    }

    /**
     * Live status check API.
     */
    public function checkStatus(string $order_code): JsonResponse
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();

        return response()->json([
            'order_code' => $order->order_code,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ]);
    }

    /**
     * Advance order status.
     */
    public function updateStatus(Request $request, string $order_code): JsonResponse|RedirectResponse
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();
        $newStatus = (string) $request->input('status');

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
