<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Warkop De Barrio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECEAE4] font-sans text-ink antialiased flex justify-center min-h-screen">
@php
    $activeTable = session('active_table', '05');
@endphp
<div class="w-full max-w-[420px] min-h-screen flex flex-col bg-cream-50 pb-[76px] shadow-xl relative">
    @if (session('success'))
        <p class="m-4 rounded-xl bg-ok-bg px-4 py-2.5 text-xs font-medium text-brand-900 shadow-sm">{{ session('success') }}</p>
    @endif
    @if (session('error'))
        <p class="m-4 rounded-xl bg-danger-bg px-4 py-2.5 text-xs font-medium text-danger shadow-sm">{{ session('error') }}</p>
    @endif

    @yield('content')

    {{-- Bottom Navigation Pelanggan (4 Tab Figma) --}}
    <nav class="fixed inset-x-0 bottom-0 mx-auto flex h-[72px] max-w-[420px] items-center border-t border-line bg-white px-2 shadow-md z-40">
        <a href="{{ route('landing', ['table' => $activeTable]) }}"
           class="flex flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('landing') ? 'font-bold text-brand-900' : 'font-medium text-muted hover:text-ink' }}">
            <svg class="size-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            <span class="text-[11px]">Beranda</span>
        </a>
        <a href="{{ route('customer.menu', ['table' => $activeTable]) }}"
           class="flex flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('customer.menu') ? 'font-bold text-brand-900' : 'font-medium text-muted hover:text-ink' }}">
            <x-lucide.coffee class="size-[22px]" />
            <span class="text-[11px]">Menu</span>
        </a>
        <a href="{{ route('orders.history', ['table' => $activeTable]) }}"
           class="flex flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('orders.history', 'pelanggan.beranda') ? 'font-bold text-brand-900' : 'font-medium text-muted hover:text-ink' }}">
            <svg class="size-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8M8 15h5" />
            </svg>
            <span class="text-[11px]">Pesanan</span>
        </a>
        <a href="{{ auth()->check() ? route('pelanggan.akun') : route('login') }}"
           class="flex flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('pelanggan.akun') ? 'font-bold text-brand-900' : 'font-medium text-muted hover:text-ink' }}">
            <x-lucide.user class="size-[22px]" />
            <span class="text-[11px]">Akun</span>
        </a>
    </nav>
</div>
</body>
</html>
