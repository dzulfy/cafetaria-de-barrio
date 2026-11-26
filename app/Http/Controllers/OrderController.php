<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display the cart screen (03 Keranjang).
     */
    public function cart(Request $request): View
    {
        $tableNumber = (string) $request->query('table', '05');

        // Retrieve cart from session, or initialize with items matching screenshot
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
        $action = (string) $request->input('action'); // 'increase', 'decrease', 'remove'

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
     * Submit order to database.
     */
    public function store(Request $request): RedirectResponse
    {
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
            'subtotal' => $subtotal,
            'total_price' => $subtotal,
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

        // Clear cart session
        session()->forget('cart');

        return redirect()->route('orders.status', ['order_code' => $order->order_code]);
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
     * Advance order status (for testing / admin / cashier).
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

    /**
     * Display order history.
     */
    public function history(Request $request): View
    {
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
}
