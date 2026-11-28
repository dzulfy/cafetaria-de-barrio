<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Menu;
use App\Models\MutasiStok;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BahanController extends Controller
{
    /**
     * Get or create a valid user ID for stock mutations.
     */
    protected function getPenggunaId(): int
    {
        if (auth()->check()) {
            return auth()->id();
        }

        $admin = User::first();
        if ($admin) {
            return $admin->id;
        }

        $newUser = User::create([
            'nama' => 'Muhammad Fikri Firdaus',
            'email' => 'admin@debarrio.id',
            'password' => bcrypt('password'),
            'peran' => 'admin',
            'status_aktif' => true,
        ]);

        return $newUser->id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Bahan::query()
            ->with(['menu' => fn ($q) => $q->select('menus.id', 'menus.name', 'menus.category_id')])
            ->orderBy('id', 'asc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search): void {
                $q->where('nama', 'like', '%'.$search.'%')
                    ->orWhereHas('menu', function ($menuQuery) use ($search): void {
                        $menuQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($status === 'menipis') {
            $query->whereColumn('stok', '<', 'stok_minimum');
        } elseif ($status === 'aman') {
            $query->whereColumn('stok', '>=', 'stok_minimum');
        }

        $bahanList = $query->paginate(7)->withQueryString();

        // Ambil semua bahan di bawah batas minimum untuk alert peringatan
        $menipisItems = Bahan::whereColumn('stok', '<', 'stok_minimum')->get();

        // Ambil semua bahan untuk dropdown modal mutasi
        $allBahan = Bahan::orderBy('nama', 'asc')->get();

        // Daftar menu untuk menghubungkan bahan dengan resep menu
        $menus = Menu::with('category')->orderBy('category_id')->orderBy('name')->get();

        return view('bahan.index', [
            'bahanList' => $bahanList,
            'menipisItems' => $menipisItems,
            'allBahan' => $allBahan,
            'menus' => $menus,
            'search' => $search,
            'status' => $status,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:bahan,nama'],
            'satuan' => ['required', 'string', 'max:20'],
            'stok' => ['required', 'numeric', 'min:0'],
            'stok_minimum' => ['required', 'numeric', 'min:0'],
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:menus,id'],
            'jumlah_pakai' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $bahan = Bahan::create([
            'nama' => $validated['nama'],
            'satuan' => $validated['satuan'],
            'stok' => $validated['stok'],
            'stok_minimum' => $validated['stok_minimum'],
        ]);

        $menuIds = array_values(array_unique(array_map('intval', $validated['menu_ids'] ?? [])));
        if ($menuIds !== []) {
            $jumlahPakai = (float) ($validated['jumlah_pakai'] ?? 1);
            $pivotData = [];
            foreach ($menuIds as $menuId) {
                $pivotData[$menuId] = ['jumlah_pakai' => $jumlahPakai];
            }
            $bahan->menu()->sync($pivotData);
        }

        if ($bahan->stok > 0) {
            MutasiStok::create([
                'bahan_id' => $bahan->id,
                'pengguna_id' => $this->getPenggunaId(),
                'jenis' => 'masuk',
                'jumlah' => $bahan->stok,
                'catatan' => 'Stok awal bahan baru',
            ]);
        }

        return redirect()->route('bahan.index')->with('success', 'Bahan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bahan $bahan): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:bahan,nama,'.$bahan->id],
            'satuan' => ['required', 'string', 'max:20'],
            'stok' => ['required', 'numeric', 'min:0'],
            'stok_minimum' => ['required', 'numeric', 'min:0'],
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:menus,id'],
            'jumlah_pakai' => ['nullable', 'numeric', 'min:0.01'],
            'sync_menus' => ['nullable', 'boolean'],
        ]);

        $selisih = $validated['stok'] - $bahan->stok;

        $bahan->update([
            'nama' => $validated['nama'],
            'satuan' => $validated['satuan'],
            'stok' => $validated['stok'],
            'stok_minimum' => $validated['stok_minimum'],
        ]);

        if ($request->boolean('sync_menus') || $request->has('menu_ids')) {
            $existingQuantities = $bahan->menu()->pluck('resep_menu.jumlah_pakai', 'menus.id')->all();
            $defaultJumlahPakai = (float) ($validated['jumlah_pakai'] ?? 1);
            $menuIds = array_values(array_unique(array_map('intval', $validated['menu_ids'] ?? [])));

            $pivotData = [];
            foreach ($menuIds as $menuId) {
                $pivotData[$menuId] = [
                    'jumlah_pakai' => (float) ($existingQuantities[$menuId] ?? $defaultJumlahPakai),
                ];
            }
            $bahan->menu()->sync($pivotData);
        }

        if ($selisih != 0) {
            MutasiStok::create([
                'bahan_id' => $bahan->id,
                'pengguna_id' => $this->getPenggunaId(),
                'jenis' => 'penyesuaian',
                'jumlah' => abs($selisih),
                'catatan' => 'Penyesuaian stok manual',
            ]);
        }

        return redirect()->route('bahan.index')->with('success', 'Bahan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bahan $bahan): RedirectResponse
    {
        // Lepas relasi resep menu & hapus mutasi terkait
        $bahan->menu()->detach();
        $bahan->mutasiStok()->delete();
        $bahan->delete();

        return redirect()->route('bahan.index')->with('success', 'Bahan berhasil dihapus.');
    }

    /**
     * Catat mutasi stok bahan.
     */
    public function mutasi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_id' => ['required', 'exists:bahan,id'],
            'jenis' => ['required', 'in:masuk,keluar,penyesuaian'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $bahan = Bahan::findOrFail($validated['bahan_id']);

        if ($validated['jenis'] === 'masuk') {
            $bahan->stok += $validated['jumlah'];
        } elseif ($validated['jenis'] === 'keluar') {
            $bahan->stok = max(0, $bahan->stok - $validated['jumlah']);
        } elseif ($validated['jenis'] === 'penyesuaian') {
            $bahan->stok = $validated['jumlah'];
        }

        $bahan->save();

        MutasiStok::create([
            'bahan_id' => $bahan->id,
            'pengguna_id' => $this->getPenggunaId(),
            'jenis' => $validated['jenis'],
            'jumlah' => $validated['jumlah'],
            'catatan' => $validated['catatan'] ?? 'Pencatatan mutasi manual',
        ]);

        return redirect()->route('bahan.index')->with('success', 'Mutasi stok berhasil dicatat.');
    }
}
