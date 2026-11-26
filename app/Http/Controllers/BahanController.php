<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
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

        $query = Bahan::query()->orderBy('id', 'asc');

        if (!empty($search)) {
            $query->where('nama', 'like', '%' . $search . '%');
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

        return view('bahan.index', [
            'bahanList' => $bahanList,
            'menipisItems' => $menipisItems,
            'allBahan' => $allBahan,
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
        ]);

        $bahan = Bahan::create($validated);

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
            'nama' => ['required', 'string', 'max:100', 'unique:bahan,nama,' . $bahan->id],
            'satuan' => ['required', 'string', 'max:20'],
            'stok' => ['required', 'numeric', 'min:0'],
            'stok_minimum' => ['required', 'numeric', 'min:0'],
        ]);

        $selisih = $validated['stok'] - $bahan->stok;

        $bahan->update($validated);

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
        // Hapus mutasi terkait jika ada
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
