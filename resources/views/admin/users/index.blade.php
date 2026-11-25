@extends('layouts.admin')
@section('title', 'Pengguna & Hak Akses')

@php
    $badge = [
        'admin'     => 'bg-brand-900 text-white',
        'kasir'     => 'bg-warn-bg text-warn-fg',
        'pelanggan' => 'bg-brand-100 text-brand-900',
    ];
    $chips = [
        null        => 'Semua ' . $counts->sum(),
        'admin'     => 'Admin ' . ($counts['admin'] ?? 0),
        'kasir'     => 'Kasir ' . ($counts['kasir'] ?? 0),
        'pelanggan' => 'Pelanggan ' . ($counts['pelanggan'] ?? 0),
    ];
@endphp

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-[26px] font-bold text-ink">Pengguna & Hak Akses</h1>
            <p class="text-[13px] text-muted">Kelola akun dan peran (Admin, Kasir, Pelanggan) Warkop De Barrio.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-[10px] bg-brand-900 px-4 py-2.5 text-[13px] font-semibold text-white shadow transition hover:bg-brand-700">
            <x-lucide.plus class="size-4" />
            <span>Tambah Pengguna</span>
        </a>
    </div>

    {{-- Filter Peran + Pencarian --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($chips as $value => $label)
                @php $on = (request('role') ?? null) === $value; @endphp
                <a href="{{ route('admin.users.index', array_filter(['role' => $value, 'q' => request('q')])) }}"
                   class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $on ? 'bg-brand-900 text-white shadow-sm' : 'border border-line bg-white text-muted hover:border-brand-900 hover:text-ink' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="sm:ml-auto w-full sm:w-72">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <div class="flex items-center gap-2.5 rounded-[10px] border border-line bg-white px-3.5 py-2 transition focus-within:border-brand-900">
                <x-lucide.search class="size-4 text-muted" />
                <input name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..."
                       class="w-full bg-transparent text-xs text-ink outline-none placeholder:text-[#9AA59E]">
                @if(request('q'))
                    <a href="{{ route('admin.users.index', array_filter(['role' => request('role')])) }}" class="text-xs text-muted hover:text-ink">×</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Pengguna --}}
    <div class="overflow-x-auto rounded-[14px] border border-line bg-white shadow-sm">
        <table class="w-full text-left text-[13px]">
            <thead class="border-b border-line bg-brand-100 font-semibold text-brand-900">
                <tr>
                    <th class="px-4 py-3">Pengguna</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Peran</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Terakhir Diubah</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($users as $u)
                    <tr class="transition hover:bg-cream-50/50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex size-9 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-900">
                                    {{ strtoupper(collect(explode(' ', $u->nama ?? $u->name))->take(2)->map(fn ($w) => $w[0] ?? '')->join('')) }}
                                </span>
                                <span class="font-semibold text-ink">{{ $u->nama ?? $u->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $badge[$u->peran ?? $u->role] ?? 'bg-brand-100 text-brand-900' }}">
                                {{ ucfirst($u->peran ?? $u->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="flex items-center gap-2 text-xs font-medium {{ ($u->status_aktif ?? $u->is_active) ? 'text-brand-900' : 'text-muted' }}"
                                        @disabled($u->id === auth()->id())
                                        title="{{ $u->id === auth()->id() ? 'Tidak dapat menonaktifkan akun sendiri' : 'Klik untuk mengubah status' }}">
                                    <span class="flex h-5 w-[34px] items-center rounded-full p-0.5 transition {{ ($u->status_aktif ?? $u->is_active) ? 'justify-end bg-brand-900' : 'justify-start bg-[#C9CFCB]' }}">
                                        <span class="size-4 rounded-full bg-white shadow-sm"></span>
                                    </span>
                                    <span>{{ ($u->status_aktif ?? $u->is_active) ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-xs text-muted">{{ $u->updated_at?->diffForHumans() ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2.5">
                                <a href="{{ route('admin.users.edit', $u) }}" title="Ubah pengguna" class="rounded p-1 text-muted transition hover:bg-brand-100 hover:text-brand-900">
                                    <x-lucide.pencil class="size-4" />
                                </a>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->nama ?? $u->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus pengguna" class="rounded p-1 text-muted transition hover:bg-danger-bg hover:text-danger">
                                            <x-lucide.trash-2 class="size-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-muted">
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $users->links() }}
    </div>

    {{-- Matriks Hak Akses Per Peran (Figma A6) --}}
    <div class="space-y-3 rounded-[14px] border border-line bg-white p-5 shadow-sm">
        <div>
            <h2 class="text-sm font-bold text-ink">Matriks Hak Akses Per Peran</h2>
            <p class="text-xs text-muted">Ringkasan hak akses tiap fitur berdasarkan rancangan sistem Warkop De Barrio.</p>
        </div>
        <div class="overflow-x-auto rounded-xl border border-line">
            <table class="w-full text-left text-xs">
                <thead class="bg-brand-100 font-semibold text-brand-900">
                    <tr>
                        <th class="px-4 py-2.5">Fitur Sistem</th>
                        <th class="w-24 px-4 py-2.5 text-center">Admin</th>
                        <th class="w-24 px-4 py-2.5 text-center">Kasir</th>
                        <th class="w-24 px-4 py-2.5 text-center">Pelanggan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ([
                        ['Kelola menu, stok bahan, pengeluaran, meja', true, false, false],
                        ['Dashboard dan laporan penjualan',          true, false, false],
                        ['Kelola pengguna dan hak akses',            true, false, false],
                        ['Proses pesanan dan pembayaran (Kasir)',    true, true,  false],
                        ['Pemesanan dan riwayat pesanan sendiri',    false, false, true],
                        ['Ubah kata sandi dan profil sendiri',       true, true,  true],
                    ] as [$fitur, $a, $k, $p])
                        <tr class="hover:bg-cream-50/50">
                            <td class="px-4 py-2.5 font-medium text-ink">{{ $fitur }}</td>
                            @foreach ([$a, $k, $p] as $allowed)
                                <td class="px-4 py-2.5 text-center">
                                    @if ($allowed)
                                        <span class="inline-flex items-center justify-center text-brand-900 font-bold">
                                            <x-lucide.check class="size-4" />
                                        </span>
                                    @else
                                        <span class="text-muted text-base leading-none">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
