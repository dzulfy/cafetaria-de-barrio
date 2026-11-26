<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerMenuController extends Controller
{
    /**
     * Display the customer menu page.
     */
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('kategori', 'semua');
        $search = $request->query('q');

        $categories = Category::withCount('menus')->get();

        $menusQuery = Menu::with('category')->latest('id');

        if ($selectedCategory && $selectedCategory !== 'semua') {
            $menusQuery->whereHas('category', function ($query) use ($selectedCategory): void {
                $query->where('slug', $selectedCategory);
            });
        }

        if (!empty($search)) {
            $menusQuery->where('name', 'like', "%{$search}%");
        }

        $menus = $menusQuery->get();

        return view('customer.menu', [
            'categories' => $categories,
            'menus' => $menus,
            'selectedCategory' => $selectedCategory,
            'search' => $search ?? '',
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
            'order_id' => 'ORD-' . strtoupper(uniqid()),
        ]);
    }
}
