<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MidtransService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerMenuController extends Controller
{
    /**
     * Resolve active table from request token, table query param, or session.
     *
     * @return array{tableNumber: string|null, cafeTable: CafeTable|null, isTableInactive: bool}
     */
    private function resolveTable(Request $request, bool $resetWhenBare = false): array
    {
        $cafeTable = null;
        $tableNumber = null;
        $rawTable = $request->query('table');
        $token = $request->query('token');

        if (! empty($token)) {
            $cafeTable = CafeTable::where('token', $token)->first();
            $tableNumber = $cafeTable?->table_number ?? $this->normalizeTableNumber((string) $rawTable);
        } elseif (! empty($rawTable)) {
            $tableNumber = $this->normalizeTableNumber((string) $rawTable);
            if ($tableNumber !== null) {
                $cafeTable = CafeTable::where('table_number', $tableNumber)->first();
            }
        } elseif ($resetWhenBare) {
            session()->forget(['active_table', 'active_table_token']);
        } else {
            $tableNumber = $this->normalizeTableNumber((string) session('active_table'));
            if ($tableNumber !== null) {
                $sessionToken = session('active_table_token');
                $cafeTable = $sessionToken
                    ? CafeTable::where('token', $sessionToken)->first()
                    : CafeTable::where('table_number', $tableNumber)->first();
            }
        }

        if ($tableNumber !== null) {
            session([
                'active_table' => $tableNumber,
                'active_table_token' => $cafeTable?->token,
            ]);
        }

        return [
            'tableNumber' => $tableNumber,
            'cafeTable' => $cafeTable,
            'isTableInactive' => $cafeTable !== null && $cafeTable->status !== 'aktif',
        ];
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
     * Handle short QR route /meja/{token} and redirect directly to customer menu page.
     */
    public function scanQr(string $token): RedirectResponse
    {
        $table = CafeTable::where('token', $token)->firstOrFail();

        session([
            'active_table' => $table->table_number,
            'active_table_token' => $table->token,
            'qr_flow' => true,
        ]);

        return redirect()->route('customer.menu', [
            'table' => $table->table_number,
            'token' => $table->token,
        ]);
    }

    /**
     * Display the customer QR landing page (Figma 01 Landing Page).
     */
    public function landing(Request $request): View
    {
        $tableData = $this->resolveTable($request, resetWhenBare: true);
        $tableNumber = $tableData['tableNumber'];

        session()->put('qr_flow', true);
        $cartKey = $this->cartSessionKey($tableNumber);
        if (! session()->has($cartKey)) {
            session()->put($cartKey, []);
        }

        $availableMenus = Menu::where('is_available', true)->orderBy('id', 'asc')->get();
        $preferredNames = ['Mix Platter', 'Indomie Goreng', 'Goodday Freeze', 'Matcha'];
        $favoriteMenus = collect();

        foreach ($preferredNames as $prefName) {
            $match = $availableMenus->first(fn (Menu $m): bool => stripos($m->name, $prefName) !== false);
            if ($match && ! $favoriteMenus->contains('id', $match->id)) {
                $favoriteMenus->push($match);
            }
        }

        foreach ($availableMenus as $menu) {
            if ($favoriteMenus->count() >= 4) {
                break;
            }
            if (! $favoriteMenus->contains('id', $menu->id)) {
                $favoriteMenus->push($menu);
            }
        }

        $minPrice = (int) (Menu::where('is_available', true)->min('price') ?? 6000);
        $cart = session()->get($cartKey, []);
        $cartCount = (int) array_sum(array_column($cart, 'quantity'));
        $activeTables = CafeTable::where('status', 'aktif')->orderBy('table_number')->get();

        return view('pelanggan.landing', [
            'tableNumber' => $tableNumber,
            'cafeTable' => $tableData['cafeTable'],
            'isTableInactive' => $tableData['isTableInactive'],
            'activeTables' => $activeTables,
            'favoriteMenus' => $favoriteMenus,
            'minPrice' => $minPrice,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Display the customer menu page (Figma 02 Menu).
     */
    public function index(Request $request): View
    {
        $tableData = $this->resolveTable($request, resetWhenBare: false);
        $tableNumber = $tableData['tableNumber'];

        session()->put('qr_flow', true);
        $cartKey = $this->cartSessionKey($tableNumber);
        if (! session()->has($cartKey)) {
            session()->put($cartKey, []);
        }

        $selectedCategory = $request->query('kategori', 'semua');
        $search = $request->query('q');

        $categories = Category::has('menus')->withCount('menus')->get();

        $menusQuery = Menu::with('category')->orderBy('id', 'asc');

        if ($selectedCategory && $selectedCategory !== 'semua') {
            $menusQuery->whereHas('category', function ($query) use ($selectedCategory): void {
                $query->where('slug', $selectedCategory);
            });
        }

        if (! empty($search)) {
            $menusQuery->where('name', 'like', "%{$search}%");
        }

        $menus = $menusQuery->get();
        $activeTables = CafeTable::where('status', 'aktif')->orderBy('table_number')->get();

        return view('customer.menu', [
            'categories' => $categories,
            'menus' => $menus,
            'selectedCategory' => $selectedCategory,
            'search' => $search ?? '',
            'cart' => session()->get($cartKey, []),
            'tableNumber' => $tableNumber,
            'isTableInactive' => $tableData['isTableInactive'],
            'activeTables' => $activeTables,
        ]);
    }

    /**
     * Display the product detail page for a specific menu item.
     */
    public function show(Request $request, Menu $menu): View
    {
        $tableData = $this->resolveTable($request, resetWhenBare: false);
        $tableNumber = $tableData['tableNumber'];

        session()->put('qr_flow', true);
        $cartKey = $this->cartSessionKey($tableNumber);
        if (! session()->has($cartKey)) {
            session()->put($cartKey, []);
        }

        $menu->load('category');
        $menuCategory = $menu->getRelationValue('category');
        $categorySlug = $menuCategory?->slug ?? (string) $menu->getRawOriginal('category');
        $cart = session()->get($cartKey, []);

        $addOnMenus = $categorySlug !== 'add-on'
            ? Menu::with('category')
                ->where('is_available', true)
                ->whereHas('category', fn ($q) => $q->where('slug', 'add-on'))
                ->orderBy('id')
                ->get()
            : collect();

        $relatedMenus = Menu::with('category')
            ->where('category_id', $menu->category_id)
            ->where('id', '!=', $menu->id)
            ->where('is_available', true)
            ->orderBy('id')
            ->limit(4)
            ->get();

        $activeTables = CafeTable::where('status', 'aktif')->orderBy('table_number')->get();

        return view('customer.menu-detail', [
            'menu' => $menu,
            'menuCategory' => $menuCategory,
            'cart' => $cart,
            'tableNumber' => $tableNumber,
            'isTableInactive' => $tableData['isTableInactive'],
            'activeTables' => $activeTables,
            'addOnMenus' => $addOnMenus,
            'relatedMenus' => $relatedMenus,
        ]);
    }

    /**
     * Handle checkout order and initiate Midtrans Snap payment.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'table_number' => ['nullable', 'string', 'max:20'],
        ]);

        $cleanTable = $this->normalizeTableNumber($validated['table_number'] ?? session('active_table')) ?? '05';
        $orderCode = Order::generateOrderCode();

        $total = 0;
        $resolvedItems = [];
        foreach ($validated['items'] as $entry) {
            $menu = Menu::findOrFail($entry['id']);
            $qty = (int) $entry['quantity'];
            $lineTotal = (int) $menu->price * $qty;
            $total += $lineTotal;
            $resolvedItems[] = [
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
                'price' => (int) $menu->price,
                'quantity' => $qty,
                'subtotal' => $lineTotal,
            ];
        }

        $order = Order::create([
            'order_code' => $orderCode,
            'table_number' => $cleanTable,
            'customer_name' => $validated['customer_name'] ?? null,
            'subtotal' => $total,
            'total_price' => $total,
            'total_amount' => $total,
            'source' => 'QR Meja',
            'status' => 'menunggu',
            'payment_status' => 'unpaid',
            'midtrans_order_id' => $orderCode,
        ]);

        foreach ($resolvedItems as $itemRow) {
            OrderItem::create(array_merge(['order_id' => $order->id], $itemRow));
        }

        try {
            app(MidtransService::class)->createSnapTransaction($order);
        } catch (\Throwable $e) {
            Log::warning('Midtrans Snap creation failed for checkout '.$order->order_code.': '.$e->getMessage());
        }

        $freshOrder = $order->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order_id' => $order->order_code,
            'snap_token' => $freshOrder?->snap_token,
            'snap_redirect_url' => $freshOrder?->snap_redirect_url,
        ]);
    }

    /**
     * Download the official printable/digital menu poster.
     */
    public function downloadMenu(): BinaryFileResponse
    {
        $path = public_path('images/daftar-menu-de-barrio.jpg');
        abort_unless(is_file($path), 404, 'File daftar menu belum tersedia.');

        return response()->download($path, 'Daftar-Menu-Cafeteria-De-Barrio.jpg', [
            'Content-Type' => 'image/jpeg',
        ]);
    }
}
