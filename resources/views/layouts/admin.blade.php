<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') | Admin De Barrio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f4ed] font-sans text-ink antialiased">
@php
    $menu = [
        ['Dashboard',       'lucide.layout-dashboard', 'admin.dashboard', 'admin.dashboard*'],
        ['Menu & Kategori', 'lucide.coffee',           'admin.menus.index', 'admin.menus.*'],
        ['Stok Bahan',      'lucide.package',          Route::has('admin.inventory.index') ? 'admin.inventory.index' : (Route::has('inventory.index') ? 'inventory.index' : null), 'admin.inventory.*'],
        ['Pengeluaran',     'lucide.wallet',           Route::has('admin.pengeluaran.index') ? 'admin.pengeluaran.index' : null, 'admin.pengeluaran.*'],
        ['Laporan',         'lucide.chart-column',     null, null],
        ['Pengguna',        'lucide.users',            'admin.users.index', 'admin.users.*'],
        ['Meja & QR',       'lucide.qr-code',          Route::has('admin.tables.index') ? 'admin.tables.index' : null, 'admin.tables.*'],
    ];
@endphp
<div class="flex min-h-screen">
    {{-- Sidebar Admin --}}
    <aside class="flex w-64 shrink-0 flex-col gap-1.5 bg-[#1F4D3A] p-4 text-white">
        <div class="flex items-center gap-2.5 px-2 pb-5 pt-2">
            <span class="flex size-10 items-center justify-center rounded-full bg-[#F6EFD8] text-[#1F4D3A]">
                <x-lucide.coffee class="size-[22px]" />
            </span>
            <div>
                <p class="font-bold leading-tight text-white">De Barrio</p>
                <p class="text-[11px] font-medium text-[#A9C4B5]">Panel Admin</p>
            </div>
        </div>

        <nav class="space-y-1">
            @foreach ($menu as [$label, $icon, $route, $pattern])
                @php $active = $pattern && request()->routeIs($pattern); @endphp
                <a href="{{ $route && Route::has($route) ? route($route) : '#' }}"
                   @if(! $route || ! Route::has($route)) title="Belum tersedia (dikerjakan modul lain)" @endif
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm transition {{ $active ? 'bg-[#2E6B52] font-bold text-white shadow-sm' : 'font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white' }} {{ $route && Route::has($route) ? '' : 'cursor-not-allowed opacity-60' }}">
                    <x-dynamic-component :component="$icon" class="size-5" />
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="mt-auto flex items-center gap-2.5 rounded-xl bg-[#2E6B52] p-3 shadow-sm">
            <span class="flex size-9 items-center justify-center rounded-full bg-[#F6EFD8] text-xs font-bold text-[#1F4D3A]">
                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'AD', 0, 2)) }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold text-white">{{ auth()->user()->nama ?? auth()->user()->name ?? 'Administrator' }}</p>
                <a href="{{ Route::has('password.edit') ? route('password.edit') : '#' }}" class="text-[11px] text-[#BFD3C7] hover:underline">Ubah Kata Sandi</a>
            </div>
            @if(auth()->check())
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar" class="text-[#BFD3C7] transition hover:text-white">
                    <x-lucide.log-out class="size-4" />
                </button>
            </form>
            @endif
        </div>
    </aside>

    {{-- Main Admin Area --}}
    <main class="flex-1 space-y-5 px-8 py-7 overflow-y-auto">
        @if (session('success'))
            <p class="rounded-xl bg-emerald-100 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="rounded-xl bg-rose-100 border border-rose-200 px-4 py-3 text-sm font-medium text-rose-900 shadow-sm">{{ session('error') }}</p>
        @endif
        @yield('content')
    </main>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>

<script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        const bgClass = type === 'success' ? 'bg-[#1b3d32] text-white border-emerald-500' : 'bg-red-800 text-white border-red-500';
        toast.className = `${bgClass} border px-4 py-3 rounded-xl shadow-lg flex items-center gap-2.5 text-sm font-medium pointer-events-auto transform transition-all duration-300 translate-y-2 opacity-0`;
        toast.innerHTML = `
            <svg class="w-4 h-4 text-emerald-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        }, 10);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@stack('scripts')
</body>
</html>
