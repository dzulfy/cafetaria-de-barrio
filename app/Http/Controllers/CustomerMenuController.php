<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerMenuController extends Controller
{
    /**
     * Resolve active table from request token, table query param, or session.
     *
     * @return array{tableNumber: string, cafeTable: CafeTable|null, isTableInactive: bool}
     */
    private function resolveTable(Request $request): array
    {
        $cafeTable = null;
        $rawTable = $request->query('table');
        $token = $request->query('token');

        if (! empty($token)) {
            $cafeTable = CafeTable::where('token', $token)->first();
        }

        if ($cafeTable) {
            $tableNumber = $cafeTable->table_number;
        } elseif (! empty($rawTable)) {
            $tableNumber = $this->normalizeTableNumber((string) $rawTable);
            $cafeTable = CafeTable::where('table_number', $tableNumber)->first();
        } else {
            $tableNumber = $this->normalizeTableNumber((string) session('active_table', '05'));
            $sessionToken = session('active_table_token');
            $cafeTable = $sessionToken
                ? CafeTable::where('token', $sessionToken)->first()
                : CafeTable::where('table_number', $tableNumber)->first();
        }

        session([
            'active_table' => $tableNumber,
            'active_table_token' => $cafeTable?->token,
        ]);

        return [
            'tableNumber' => $tableNumber,
            'cafeTable' => $cafeTable,
            'isTableInactive' => $cafeTable !== null && $cafeTable->status !== 'aktif',
        ];
    }

    private function normalizeTableNumber(?string $raw): string
    {
        $cleaned = trim((string) preg_replace('/^Meja\s+/i', '', (string) $raw));

        if ($cleaned === '') {
            return '05';
        }

        if (ctype_digit($cleaned) && strlen($cleaned) === 1) {
            return str_pad($cleaned, 2, '0', STR_PAD_LEFT);
        }

        return $cleaned;
    }

    /**
     * Handle short QR route /meja/{token} and redirect to customer landing page.
     */
    public function scanQr(string $token): RedirectResponse
    {
        $table = CafeTable::where('token', $token)->firstOrFail();

        session([
            'active_table' => $table->table_number,
            'active_table_token' => $table->token,
            'qr_flow' => true,
        ]);

        return redirect()->route('landing', [
            'table' => $table->table_number,
            'token' => $table->token,
        ]);
    }

    /**
     * Display the customer QR landing page (Figma 01 Landing Page).
     */
    public function landing(Request $request): View
    {
        $tableData = $this->resolveTable($request);

        session()->put('qr_flow', true);
        if (! session()->has('cart')) {
            session()->put('cart', []);
        }

        $availableMenus = Menu::where('is_available', true)->orderBy('id', 'asc')->get();
        $preferredNames = ['Kopi Susu Gula Aren', 'V60 Manual Brew'];
        $favoriteMenus = collect();

        foreach ($preferredNames as $prefName) {
            $match = $availableMenus->first(fn (Menu $m): bool => stripos($m->name, $prefName) !== false);
            if ($match && ! $favoriteMenus->contains('id', $match->id)) {
                $favoriteMenus->push($match);
            }
        }

        foreach ($availableMenus as $menu) {
            if ($favoriteMenus->count() >= 2) {
                break;
            }
            if (! $favoriteMenus->contains('id', $menu->id)) {
                $favoriteMenus->push($menu);
            }
        }

        $minPrice = (int) (Menu::where('is_available', true)->min('price') ?? 6000);
        $cart = session()->get('cart', []);
        $cartCount = (int) array_sum(array_column($cart, 'quantity'));

        return view('pelanggan.landing', [
            'tableNumber' => $tableData['tableNumber'],
            'cafeTable' => $tableData['cafeTable'],
            'isTableInactive' => $tableData['isTableInactive'],
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
        $tableData = $this->resolveTable($request);

        session()->put('qr_flow', true);
        if (! session()->has('cart')) {
            session()->put('cart', []);
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

        return view('customer.menu', [
            'categories' => $categories,
            'menus' => $menus,
            'selectedCategory' => $selectedCategory,
            'search' => $search ?? '',
            'cart' => session()->get('cart', []),
            'tableNumber' => $tableData['tableNumber'],
            'isTableInactive' => $tableData['isTableInactive'],
        ]);
    }

    /**
     * Handle checkout order simulation.
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

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order_id' => 'ORD-'.strtoupper(uniqid()),
        ]);
    }
}
