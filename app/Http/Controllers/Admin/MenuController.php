<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of menus in Admin Panel.
     */
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('kategori', 'semua');
        $search = $request->query('q');

        $categories = Category::withCount('menus')->get();
        $totalMenusCount = Menu::count();

        $menusQuery = Menu::with('category')->orderBy('id', 'asc');

        if ($selectedCategory && $selectedCategory !== 'semua') {
            $menusQuery->whereHas('category', function ($query) use ($selectedCategory): void {
                $query->where('slug', $selectedCategory);
            });
        }

        if (! empty($search)) {
            $menusQuery->where('name', 'like', "%{$search}%");
        }

        $menus = $menusQuery->paginate(7)->withQueryString();

        return view('admin.menus.index', [
            'categories' => $categories,
            'menus' => $menus,
            'totalMenusCount' => $totalMenusCount,
            'selectedCategory' => $selectedCategory,
            'search' => $search ?? '',
        ]);
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_available' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create([
            'name' => $validated['name'],
            'category_id' => (int) $validated['category_id'],
            'price' => (int) $validated['price'],
            'description' => $validated['description'] ?? null,
            'is_available' => $request->boolean('is_available', true),
            'image' => $imagePath,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil ditambahkan!',
                'menu' => $menu->load('category'),
            ]);
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, Menu $menu): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_available' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data = [
            'name' => $validated['name'],
            'category_id' => (int) $validated['category_id'],
            'price' => (int) $validated['price'],
            'description' => $validated['description'] ?? null,
            'is_available' => $request->boolean('is_available', false),
        ];

        if ($request->hasFile('image')) {
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil diperbarui!',
                'menu' => $menu->load('category'),
            ]);
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy(Request $request, Menu $menu): RedirectResponse|JsonResponse
    {
        if ($menu->image && Storage::disk('public')->exists($menu->image)) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus!',
            ]);
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * Toggle availability status of a menu.
     */
    public function toggleStatus(Request $request, Menu $menu): JsonResponse
    {
        $menu->is_available = ! $menu->is_available;
        $menu->save();

        return response()->json([
            'success' => true,
            'is_available' => $menu->is_available,
            'message' => 'Status menu diperbarui: '.($menu->is_available ? 'Tersedia' : 'Habis'),
        ]);
    }
}
