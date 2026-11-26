<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\SyncQueue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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

        // Selected order defaults to DB-0124 (from Figma) or first menunggu order
        $selectedOrder = $orders->firstWhere('order_code', 'DB-0124')
            ?? $orders->firstWhere('status', 'menunggu')
            ?? $orders->first();

        // Sync Queue & Stats (K6 Sinkronisasi Offline)
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
        $validated = $request->validate([
            'payment_method' => 'required|in:tunai,qris,transfer',
            'amount_paid' => 'required|numeric|min:'.$order->total_amount,
        ]);

        $amountPaid = (int) $validated['amount_paid'];
        $changeAmount = $amountPaid - $order->total_amount;

        $order->update([
            'status' => 'selesai',
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
     * Create a new order (from Kasir or QR code).
     */
    public function store(Request $request): JsonResponse
    {
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

        // Generate next DB-XXXX order code
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

        $order->update(['total_amount' => $total]);

        return response()->json([
            'success' => true,
            'message' => "Pesanan baru {$orderCode} berhasil dibuat!",
            'order' => $order->fresh(['items.menuItem']),
        ], 201);
    }

    /**
     * Display transaction history.
     */
    public function history(Request $request): View|JsonResponse
    {
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

        return view('kasir.riwayat', compact('historyOrders'));
    }

    /**
     * Customer self-service QR ordering page (simulation of mobile).
     */
    public function customerQr(Request $request): View
    {
        $tableNumber = $request->query('table', 'Meja 05');
        $categories = Category::with(['menuItems' => function ($q) {
            $q->where('is_available', true);
        }])->get();

        return view('customer.order', compact('tableNumber', 'categories'));
    }
}
