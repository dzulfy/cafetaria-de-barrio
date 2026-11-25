<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('nama', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")))
            ->when($request->role, fn ($q, $r) => $q->where('peran', $r))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $counts = User::selectRaw('peran, count(*) as total')->groupBy('peran')->pluck('total', 'peran');

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'peran' => $request->role,
            'status_aktif' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = [
            'nama' => $request->name,
            'email' => $request->email,
            'peran' => $request->role,
            'status_aktif' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        // Admin tidak boleh menurunkan peran atau menonaktifkan akun sendiri
        if ($user->id === auth()->id()) {
            $data['peran'] = $user->peran;
            $data['status_aktif'] = true;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggle(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['status_aktif' => ! $user->status_aktif]);

        return back()->with('success', 'Status pengguna berhasil diubah.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        try {
            $user->delete();
        } catch (QueryException) {
            return back()->with('error', 'Pengguna memiliki riwayat pesanan, pembayaran, atau pengeluaran. Nonaktifkan saja akunnya.');
        }

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
