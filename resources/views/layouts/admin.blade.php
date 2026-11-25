<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Admin De Barrio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 font-sans text-ink antialiased">
@php
    $menu = [
        ['Dashboard',       'lucide.layout-dashboard', 'admin.dashboard', 'admin.dashboard'],
        ['Menu & Kategori', 'lucide.coffee',           null, null],
        ['Stok Bahan',      'lucide.package',          null, null],
        ['Pengeluaran',     'lucide.wallet',           null, null],
        ['Laporan',         'lucide.chart-column',     null, null],
        ['Pengguna',        'lucide.users',            'admin.users.index', 'admin.users.*'],
        ['Meja & QR',       'lucide.qr-code',          null, null],
    ];
@endphp
<div class="flex min-h-screen">
    {{-- Sidebar Admin --}}
    <aside class="flex w-60 shrink-0 flex-col gap-1.5 bg-brand-900 p-4">
        <div class="flex items-center gap-2.5 px-2 pb-5 pt-2">
            <span class="flex size-10 items-center justify-center rounded-full bg-cream text-brand-900">
                <x-lucide.coffee class="size-[22px]" />
            </span>
            <div>
                <p class="font-bold leading-tight text-white">De Barrio</p>
                <p class="text-[11px] font-medium text-[#A9C4B5]">Panel Admin</p>
            </div>
        </div>

        @foreach ($menu as [$label, $icon, $route, $pattern])
            @php $active = $pattern && request()->routeIs($pattern); @endphp
            <a href="{{ $route ? route($route) : '#' }}"
               @if(! $route) title="Belum tersedia (dikerjakan modul lain)" @endif
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm transition {{ $active ? 'bg-brand-700 font-bold text-white shadow-sm' : 'font-medium text-[#A9C4B5] hover:bg-brand-700/50 hover:text-white' }} {{ $route ? '' : 'cursor-not-allowed opacity-60' }}">
                <x-dynamic-component :component="$icon" class="size-5" />
                {{ $label }}
            </a>
        @endforeach

        <div class="mt-auto flex items-center gap-2.5 rounded-xl bg-brand-700 p-3 shadow-sm">
            <span class="flex size-9 items-center justify-center rounded-full bg-cream text-xs font-bold text-brand-900">
                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 2)) }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold text-white">{{ auth()->user()->nama ?? auth()->user()->name }}</p>
                <a href="{{ route('password.edit') }}" class="text-[11px] text-[#BFD3C7] hover:underline">Ubah Kata Sandi</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar" class="text-[#BFD3C7] transition hover:text-white">
                    <x-lucide.log-out class="size-4" />
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Admin Area --}}
    <main class="flex-1 space-y-5 px-8 py-7">
        @if (session('success'))
            <p class="rounded-xl bg-ok-bg px-4 py-3 text-sm font-medium text-brand-900 shadow-sm">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="rounded-xl bg-danger-bg px-4 py-3 text-sm font-medium text-danger shadow-sm">{{ session('error') }}</p>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
