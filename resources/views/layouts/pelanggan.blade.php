<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Warkop De Barrio</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-de-barrio.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 font-sans text-ink antialiased flex flex-col min-h-screen">
@php
    $activeTable = session('active_table');
    $tableParams = $activeTable ? ['table' => $activeTable] : [];
@endphp
<div class="w-full min-h-screen flex flex-col bg-cream-50 pb-[76px] md:pb-0 relative">
    {{-- Desktop / Tablet Top Navbar --}}
    <header class="hidden md:block sticky top-0 z-40 bg-cream-50/95 backdrop-blur-md border-b border-line">
        <div class="max-w-5xl mx-auto h-16 px-6 flex items-center justify-between gap-4">
            <a href="{{ route('landing', $tableParams) }}" class="flex items-center gap-2.5 group">
                <div class="size-9 rounded-full bg-cream overflow-hidden border border-brand-900/15 flex items-center justify-center shadow-2xs transition-transform duration-300 group-hover:-rotate-6 shrink-0">
                    <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafeteria De Barrio" class="w-full h-full object-cover">
                </div>
                <span class="font-bold text-[17px] text-ink tracking-tight">De Barrio</span>
            </a>

            <nav class="flex items-center gap-1.5">
                <a href="{{ route('landing', $tableParams) }}"
                   class="px-3.5 py-2 rounded-xl text-xs transition {{ request()->routeIs('landing') ? 'bg-brand-100 text-brand-900 font-semibold' : 'font-medium text-muted hover:text-ink hover:bg-white/70' }}">
                    Beranda
                </a>
                <a href="{{ route('customer.menu', $tableParams) }}"
                   class="px-3.5 py-2 rounded-xl text-xs transition {{ request()->routeIs('customer.menu') ? 'bg-brand-100 text-brand-900 font-semibold' : 'font-medium text-muted hover:text-ink hover:bg-white/70' }}">
                    Menu
                </a>
                <a href="{{ route('orders.history', $tableParams) }}"
                   class="px-3.5 py-2 rounded-xl text-xs transition {{ request()->routeIs('orders.history', 'pelanggan.beranda') ? 'bg-brand-100 text-brand-900 font-semibold' : 'font-medium text-muted hover:text-ink hover:bg-white/70' }}">
                    Pesanan
                </a>
                <a href="{{ auth()->check() ? route('pelanggan.akun') : route('login') }}"
                   class="px-3.5 py-2 rounded-xl text-xs transition {{ request()->routeIs('pelanggan.akun') ? 'bg-brand-100 text-brand-900 font-semibold' : 'font-medium text-muted hover:text-ink hover:bg-white/70' }}">
                    Akun
                </a>
            </nav>

            <span class="inline-flex items-center gap-1.5 rounded-full {{ $activeTable ? 'bg-brand-100 text-brand-900' : 'bg-white border border-line text-muted' }} px-3 py-1.5 text-xs font-semibold">
                <x-lucide.qr-code class="size-3.5 shrink-0" />
                <span>{{ $activeTable ? 'Meja ' . $activeTable : 'Scan QR Meja' }}</span>
            </span>
        </div>
    </header>

    <main class="w-full max-w-5xl mx-auto flex-1">
        @if (session('success'))
            <p class="mx-5 mt-4 rounded-xl bg-ok-bg px-4 py-2.5 text-xs font-medium text-brand-900 shadow-sm animate-fade-up">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="mx-5 mt-4 rounded-xl bg-danger-bg px-4 py-2.5 text-xs font-medium text-danger shadow-sm animate-fade-up">{{ session('error') }}</p>
        @endif

        @yield('content')
    </main>

    {{-- Mobile Bottom Navigation (4 Tab Figma) --}}
    <nav class="md:hidden fixed inset-x-0 bottom-0 flex h-[72px] items-center border-t border-line bg-white/95 backdrop-blur-md px-2 shadow-md z-40">
        <a href="{{ route('landing', $tableParams) }}"
           class="flex flex-1 flex-col items-center justify-center gap-1 py-1.5 rounded-xl transition-transform active:scale-95 {{ request()->routeIs('landing') ? 'font-bold text-brand-900' : 'font-medium text-muted hover:text-ink' }}">
            <svg class="size-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            <span class="text-[11px]">Beranda</span>
        </a>
        <a href="{{ route('customer.menu', $tableParams) }}"
           class="flex flex-1 flex-col items-center justify-center gap-1 py-1.5 rounded-xl transition-transform active:scale-95 {{ request()->routeIs('customer.menu') ? 'font-bold text-brand-900' : 'font-medium text-muted hover:text-ink' }}">
            <x-lucide.coffee class="size-[22px]" />
            <span class="text-[11px]">Menu</span>
        </a>
        <a href="{{ route('orders.history', $tableParams) }}"
           class="flex flex-1 flex-col items-center justify-center gap-1 py-1.5 rounded-xl transition-transform active:scale-95 {{ request()->routeIs('orders.history', 'pelanggan.beranda') ? 'font-bold text-brand-900' : 'font-medium text-muted hover:text-ink' }}">
            <svg class="size-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8M8 15h5" />
            </svg>
            <span class="text-[11px]">Pesanan</span>
        </a>
        <a href="{{ auth()->check() ? route('pelanggan.akun') : route('login') }}"
           class="flex flex-1 flex-col items-center justify-center gap-1 py-1.5 rounded-xl transition-transform active:scale-95 {{ request()->routeIs('pelanggan.akun') ? 'font-bold text-brand-900' : 'font-medium text-muted hover:text-ink' }}">
            <x-lucide.user class="size-[22px]" />
            <span class="text-[11px]">Akun</span>
        </a>
    </nav>
</div>
</body>
</html>
