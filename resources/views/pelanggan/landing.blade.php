<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Warkop De Barrio{{ !empty($tableNumber) ? ' — Meja ' . $tableNumber : '' }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-de-barrio.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 font-sans text-ink antialiased flex flex-col min-h-screen">
@php
    $tableNumber = $tableNumber ?? null;
    $tableParams = $tableNumber ? ['table' => $tableNumber] : [];
    $isTableInactive = $isTableInactive ?? false;
    $activeTables = $activeTables ?? collect();
    $favoriteMenus = $favoriteMenus ?? collect();
    $minPrice = $minPrice ?? 6000;
    $cartCount = $cartCount ?? 0;
@endphp

<div class="w-full min-h-screen bg-cream-50 flex flex-col">
    {{-- Responsive Sticky Top Bar --}}
    <header class="sticky top-0 z-40 bg-cream-50/95 backdrop-blur-md border-b border-line/60">
        <div class="max-w-6xl mx-auto h-16 px-5 md:px-8 flex items-center justify-between gap-4">
            <a href="{{ route('landing', $tableParams) }}" class="flex items-center gap-2.5 group">
                <div class="size-9 rounded-full bg-cream overflow-hidden border border-brand-900/15 flex items-center justify-center shadow-2xs transition-transform duration-300 group-hover:-rotate-6 shrink-0">
                    <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafeteria De Barrio" class="w-full h-full object-cover">
                </div>
                <span class="font-bold text-[17px] text-ink tracking-tight">De Barrio</span>
            </a>

            <nav class="hidden md:flex items-center gap-1.5">
                <a href="{{ route('landing', $tableParams) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-brand-100 text-brand-900 transition">
                    Beranda
                </a>
                <a href="{{ route('customer.menu', $tableParams) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-medium text-muted hover:text-ink hover:bg-white/80 transition">
                    Menu
                </a>
                <a href="{{ route('orders.history', $tableParams) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-medium text-muted hover:text-ink hover:bg-white/80 transition">
                    Pesanan
                </a>
            </nav>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('cart', $tableParams) }}"
                   class="hidden md:inline-flex items-center gap-1.5 rounded-[10px] border border-line bg-white px-3.5 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:bg-brand-100 active:scale-95">
                    <svg class="size-3.5 text-brand-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Keranjang</span>
                    @if ($cartCount > 0)
                        <span class="rounded-full bg-brand-900 px-1.5 py-0.5 text-[10px] font-bold text-white leading-none">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    @if (auth()->user()->hasRole('pelanggan'))
                        <a href="{{ route('pelanggan.akun') }}"
                           class="rounded-[10px] border border-line bg-white px-3.5 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:bg-brand-100 active:scale-95">
                            Akun
                        </a>
                    @else
                        <a href="{{ route(auth()->user()->homeRoute()) }}"
                           class="rounded-[10px] border border-line bg-white px-3.5 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:bg-brand-100 active:scale-95">
                            Panel {{ ucfirst(auth()->user()->peran ?? auth()->user()->role) }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="rounded-[10px] border border-line bg-white px-3.5 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:bg-brand-100 active:scale-95">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Responsive Hero Section --}}
    <section class="bg-brand-900 text-white overflow-hidden">
        <div class="max-w-6xl mx-auto px-5 md:px-8 py-7 md:py-14 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 animate-fade-up">
                <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-700/90 px-3 py-1.5 text-xs font-medium text-white shadow-2xs">
                    <x-lucide.qr-code class="size-3.5 shrink-0" />
                    @if ($tableNumber)
                        <span>Kamu memesan dari Meja {{ $tableNumber }}</span>
                    @else
                        <span>Scan QR di meja untuk memesan</span>
                    @endif
                </div>

                @if ($isTableInactive)
                    <div class="mt-3 rounded-xl bg-danger-bg px-3.5 py-2.5 text-xs font-semibold text-danger border border-danger/20">
                        Meja ini sedang nonaktif. Silakan hubungi kasir.
                    </div>
                @endif

                <h1 class="mt-3.5 text-[28px] sm:text-4xl lg:text-[42px] font-extrabold leading-[1.18] tracking-tight text-white">
                    Ngopi manual brew,<br>harga anak kos.
                </h1>

                <p class="mt-3 text-[13px] sm:text-sm lg:text-[15px] leading-[1.6] text-[#D7E5DC] max-w-xl">
                    Warkop Cafetaria De Barrio tempat nongkrong, ngerjain tugas, dan ngopi tanpa bikin dompet menjerit.
                </p>

                <div class="mt-6 grid grid-cols-2 sm:flex sm:flex-wrap sm:items-center gap-2.5 sm:gap-3">
                    <a href="{{ route('customer.menu', $tableParams) }}"
                       class="h-11 sm:px-6 rounded-xl bg-cream text-brand-900 font-semibold text-[13px] sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-all duration-200 hover:bg-[#efe6ca] hover:-translate-y-0.5 active:scale-[0.98]">
                        <x-lucide.coffee class="size-[18px]" />
                        <span>Lihat Menu</span>
                    </a>

                    <a href="{{ route('cart', $tableParams) }}"
                       class="h-11 sm:px-6 rounded-xl bg-brand-700 text-white font-semibold text-[13px] sm:text-sm flex items-center justify-center gap-2 border border-white/10 transition-all duration-200 hover:bg-brand-700/80 hover:-translate-y-0.5 active:scale-[0.98] relative">
                        <svg class="size-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Keranjang</span>
                        @if ($cartCount > 0)
                            <span class="rounded-full bg-cream px-1.5 py-0.5 text-[10px] font-bold text-brand-900 leading-none">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('customer.menu.download') }}"
                       download="Daftar-Menu-Cafeteria-De-Barrio.jpg"
                       class="col-span-2 sm:col-span-1 h-11 sm:px-5 rounded-xl bg-white/12 text-white font-semibold text-[13px] sm:text-sm flex items-center justify-center gap-2 border border-white/20 transition-all duration-200 hover:bg-white/20 hover:-translate-y-0.5 active:scale-[0.98]">
                        <svg class="size-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        <span>Download Menu</span>
                    </a>
                </div>

                @if (! $tableNumber && $activeTables->isNotEmpty())
                    <div class="mt-5 pt-4 border-t border-white/10">
                        <p class="text-[11px] font-medium text-[#D7E5DC] mb-2">Atau pilih meja secara langsung:</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($activeTables as $tbl)
                                <a href="{{ $tbl->qr_url }}"
                                   class="px-2.5 py-1 rounded-lg bg-brand-700/80 hover:bg-cream hover:text-brand-900 text-white text-[11px] font-semibold transition active:scale-95">
                                    {{ $tbl->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Desktop Decorative Ordering Guide Card --}}
            <div class="hidden lg:flex lg:col-span-5 justify-end animate-fade-up" style="animation-delay: 120ms;">
                <div class="w-full max-w-sm rounded-3xl bg-brand-700/45 border border-white/15 backdrop-blur-sm p-6 shadow-xl space-y-5">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded-full bg-cream overflow-hidden border border-white/30 flex items-center justify-center shadow-sm animate-float-soft shrink-0">
                                <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafeteria De Barrio" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-xs font-medium text-[#D7E5DC]">Self-Order QR</p>
                                <h2 class="text-sm font-bold text-white">Pesan Langsung dari Meja</h2>
                            </div>
                        </div>
                        <span class="rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-semibold text-cream shrink-0">
                            {{ $tableNumber ? 'Meja ' . $tableNumber : 'Belum pilih meja' }}
                        </span>
                    </div>

                    <ol class="space-y-3 text-xs text-[#D7E5DC]">
                        <li class="flex items-center gap-3">
                            <span class="size-6 rounded-full bg-cream text-brand-900 font-bold flex items-center justify-center shrink-0">1</span>
                            <span>Pilih kopi, minuman, atau camilan favoritmu</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="size-6 rounded-full bg-cream text-brand-900 font-bold flex items-center justify-center shrink-0">2</span>
                            <span>Tambahkan catatan & kirim pesanan ke dapur</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="size-6 rounded-full bg-cream text-brand-900 font-bold flex items-center justify-center shrink-0">3</span>
                            <span>Bayar di kasir & pantau status secara langsung</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- Info Cards --}}
    <section class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-6 md:pt-10 grid grid-cols-3 gap-2.5 sm:gap-4 md:gap-5">
        <div class="reveal-on-scroll bg-white border border-line rounded-2xl p-3 sm:p-4 md:p-5 flex flex-col gap-1.5 shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-brand-900/30">
            <svg class="size-[18px] sm:size-5 text-brand-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
            <span class="text-[11px] sm:text-xs font-medium text-muted">Lokasi</span>
            <span class="text-xs sm:text-sm font-bold text-ink leading-snug">Telukjambe Timur, Karawang</span>
        </div>

        <div class="reveal-on-scroll bg-white border border-line rounded-2xl p-3 sm:p-4 md:p-5 flex flex-col gap-1.5 shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-brand-900/30" style="transition-delay: 60ms;">
            <x-lucide.clock class="size-[18px] sm:size-5 text-brand-900" />
            <span class="text-[11px] sm:text-xs font-medium text-muted">Jam buka</span>
            <span class="text-xs sm:text-sm font-bold text-ink leading-snug">10.00 - 23.00</span>
        </div>

        <div class="reveal-on-scroll bg-white border border-line rounded-2xl p-3 sm:p-4 md:p-5 flex flex-col gap-1.5 shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-brand-900/30" style="transition-delay: 120ms;">
            <x-lucide.wallet class="size-[18px] sm:size-5 text-brand-900" />
            <span class="text-[11px] sm:text-xs font-medium text-muted">Harga</span>
            <span class="text-xs sm:text-sm font-bold text-ink leading-snug">Mulai Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
        </div>
    </section>

    {{-- Menu Favorit --}}
    <section class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-7 md:pt-10 reveal-on-scroll">
        <div class="flex items-center justify-between">
            <h2 class="text-[15px] sm:text-lg font-bold text-ink">Menu Favorit</h2>
            <a href="{{ route('customer.menu', $tableParams) }}"
               class="inline-flex items-center gap-1 text-xs sm:text-sm font-semibold text-brand-900 hover:underline group">
                <span>Lihat semua</span>
                <span class="transition-transform duration-200 group-hover:translate-x-0.5">&rarr;</span>
            </a>
        </div>

        <div class="mt-3.5 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            @forelse ($favoriteMenus as $fav)
                <a href="{{ route('customer.menu.show', array_merge(['menu' => $fav->id], $tableParams)) }}"
                   class="group bg-white border border-line rounded-2xl p-2.5 sm:p-3 flex flex-col shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-brand-900/35 active:scale-[0.98]">
                    <div class="h-24 sm:h-28 rounded-xl bg-cream flex items-center justify-center text-gold overflow-hidden mb-2.5">
                        @if ($fav->image)
                            <img src="{{ asset('storage/' . $fav->image) }}" alt="{{ $fav->name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        @else
                            <x-lucide.coffee class="size-8 transition-transform duration-300 group-hover:scale-110" />
                        @endif
                    </div>
                    <h3 class="text-[13px] sm:text-sm font-bold text-ink leading-tight truncate">{{ $fav->name }}</h3>
                    <p class="text-[13px] sm:text-sm font-bold text-brand-900 mt-1">{{ $fav->formatted_price }}</p>
                </a>
            @empty
                <a href="{{ route('customer.menu', $tableParams) }}"
                   class="group bg-white border border-line rounded-2xl p-2.5 sm:p-3 flex flex-col shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                    <div class="h-24 sm:h-28 rounded-xl bg-cream flex items-center justify-center text-gold mb-2.5">
                        <x-lucide.coffee class="size-8 transition-transform duration-300 group-hover:scale-110" />
                    </div>
                    <h3 class="text-[13px] sm:text-sm font-bold text-ink leading-tight truncate">Mix Platter</h3>
                    <p class="text-[13px] sm:text-sm font-bold text-brand-900 mt-1">Rp 15.000</p>
                </a>
                <a href="{{ route('customer.menu', $tableParams) }}"
                   class="group bg-white border border-line rounded-2xl p-2.5 sm:p-3 flex flex-col shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                    <div class="h-24 sm:h-28 rounded-xl bg-cream flex items-center justify-center text-gold mb-2.5">
                        <x-lucide.coffee class="size-8 transition-transform duration-300 group-hover:scale-110" />
                    </div>
                    <h3 class="text-[13px] sm:text-sm font-bold text-ink leading-tight truncate">Indomie Goreng</h3>
                    <p class="text-[13px] sm:text-sm font-bold text-brand-900 mt-1">Rp 10.000</p>
                </a>
            @endforelse
        </div>
    </section>

    {{-- Daftar Menu Resmi & Download Poster --}}
    <section class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-7 md:pt-10 reveal-on-scroll">
        <div class="bg-white border border-line rounded-3xl p-5 sm:p-6 md:p-8 shadow-2xs grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            <div class="md:col-span-4 flex justify-center">
                <button type="button"
                        onclick="openMenuPosterModal()"
                        class="group relative w-48 sm:w-56 rounded-2xl overflow-hidden border border-line bg-cream shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-brand-900/30"
                        aria-label="Perbesar Poster Daftar Menu Cafeteria De Barrio">
                    <img src="{{ asset('images/daftar-menu-de-barrio.jpg') }}"
                         alt="Poster Daftar Menu Cafeteria De Barrio"
                         class="w-full h-auto object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                    <span class="absolute bottom-2.5 right-2.5 rounded-full bg-brand-900/90 text-white text-[11px] font-semibold px-2.5 py-1 shadow">
                        Perbesar
                    </span>
                </button>
            </div>

            <div class="md:col-span-8 flex flex-col gap-3">
                <div class="inline-flex items-center gap-1.5 self-start rounded-full bg-brand-100 px-3 py-1 text-[11px] font-semibold text-brand-900">
                    <span>Menu Resmi Cafeteria De Barrio • Free Wi-Fi</span>
                </div>
                <h2 class="text-lg sm:text-xl md:text-2xl font-extrabold text-ink tracking-tight">
                    Simpan & Unduh Daftar Menu Lengkap
                </h2>
                <p class="text-[13px] sm:text-sm text-muted leading-relaxed">
                    Mau pilih-pilih bareng teman sebelum pesan atau simpan daftar harga? Unduh poster daftar menu resmi kami lengkap dengan harga Minuman, Snack, Mie, dan Add On.
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                    <div class="rounded-xl bg-cream-50 border border-line px-3 py-2">
                        <p class="text-[11px] text-muted font-medium">Minuman</p>
                        <p class="text-xs font-bold text-ink">Rp 6K – 10K</p>
                    </div>
                    <div class="rounded-xl bg-cream-50 border border-line px-3 py-2">
                        <p class="text-[11px] text-muted font-medium">Snack</p>
                        <p class="text-xs font-bold text-ink">Rp 12K – 15K</p>
                    </div>
                    <div class="rounded-xl bg-cream-50 border border-line px-3 py-2">
                        <p class="text-[11px] text-muted font-medium">Mie</p>
                        <p class="text-xs font-bold text-ink">Rp 10K</p>
                    </div>
                    <div class="rounded-xl bg-cream-50 border border-line px-3 py-2">
                        <p class="text-[11px] text-muted font-medium">Add On</p>
                        <p class="text-xs font-bold text-ink">Rp 5K</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('customer.menu.download') }}"
                       download="Daftar-Menu-Cafeteria-De-Barrio.jpg"
                       class="h-11 px-5 rounded-xl bg-brand-900 text-white font-semibold text-[13px] sm:text-sm inline-flex items-center justify-center gap-2 shadow-xs transition-all duration-200 hover:bg-brand-700 hover:-translate-y-0.5 active:scale-[0.98]">
                        <svg class="size-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        <span>Download Menu (JPG)</span>
                    </a>
                    <button type="button"
                            onclick="openMenuPosterModal()"
                            class="h-11 px-5 rounded-xl bg-cream text-brand-900 border border-line font-semibold text-[13px] sm:text-sm inline-flex items-center justify-center gap-2 transition-all duration-200 hover:bg-[#efe6ca] active:scale-[0.98] cursor-pointer">
                        <span>Lihat Poster Menu</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Preview Poster Menu --}}
    <div id="menu-poster-modal"
         onclick="if (event.target === this) closeMenuPosterModal()"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="menu-poster-modal-title">
        <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-line">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-line">
                <h3 id="menu-poster-modal-title" class="text-sm font-bold text-ink">Daftar Menu Cafeteria De Barrio</h3>
                <button type="button"
                        onclick="closeMenuPosterModal()"
                        class="size-8 rounded-full bg-cream-50 text-ink hover:bg-line/50 flex items-center justify-center transition cursor-pointer"
                        aria-label="Tutup">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto bg-cream-50 flex justify-center">
                <img src="{{ asset('images/daftar-menu-de-barrio.jpg') }}"
                     alt="Poster Daftar Menu Cafeteria De Barrio"
                     class="max-h-[65vh] w-auto rounded-2xl border border-line shadow-sm">
            </div>
            <div class="px-5 py-3.5 border-t border-line bg-white flex items-center justify-end gap-2.5">
                <button type="button"
                        onclick="closeMenuPosterModal()"
                        class="h-10 px-4 rounded-xl border border-line text-xs font-semibold text-muted hover:text-ink transition cursor-pointer">
                    Tutup
                </button>
                <a href="{{ route('customer.menu.download') }}"
                   download="Daftar-Menu-Cafeteria-De-Barrio.jpg"
                   class="h-10 px-4 rounded-xl bg-brand-900 text-white text-xs font-semibold inline-flex items-center gap-1.5 hover:bg-brand-700 transition">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    <span>Download Menu</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Tentang Kami & Lokasi --}}
    <section class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-7 md:pt-10 pb-8 md:pb-12 grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 items-start">
        <div class="reveal-on-scroll md:bg-white md:border md:border-line md:rounded-2xl md:p-6 md:shadow-2xs">
            <h2 class="text-[15px] sm:text-lg font-bold text-ink">Tentang Kami</h2>
            <p class="mt-2 text-[13px] sm:text-sm leading-[1.65] text-muted">
                Warkop dengan penyajian kopi manual brew. Harga dijaga tetap murah, mayoritas di bawah Rp 20.000, cocok untuk mahasiswa dan anak kos.
            </p>
        </div>

        <div class="reveal-on-scroll md:bg-white md:border md:border-line md:rounded-2xl md:p-6 md:shadow-2xs">
            <div class="flex items-center justify-between">
                <h2 class="text-[15px] sm:text-lg font-bold text-ink">Lokasi</h2>
                <button type="button"
                        onclick="copyAddress(this)"
                        class="text-[11px] font-semibold text-brand-900 bg-brand-100 hover:bg-brand-900 hover:text-white px-2.5 py-1 rounded-lg transition cursor-pointer active:scale-95">
                    Salin Alamat
                </button>
            </div>
            <div class="mt-2.5 h-[120px] md:h-[140px] rounded-2xl bg-brand-100 flex items-center justify-center text-brand-900 group overflow-hidden">
                <svg class="size-9 animate-float-soft" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
            </div>
            <p id="cafe-address" class="mt-2.5 text-xs sm:text-[13px] leading-relaxed text-muted">
                Jl. HS. Ronggo Waluyo No. 4, Desa Krajan, Telukjambe Timur, Kab. Karawang
            </p>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="mt-auto bg-brand-900 py-4 md:py-5 px-5 text-center text-[11px] sm:text-xs font-medium text-[#D7E5DC]">
        &copy; 2026 Warkop Cafetaria De Barrio
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reveals = document.querySelectorAll('.reveal-on-scroll');
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08 });
            reveals.forEach((el) => observer.observe(el));
        } else {
            reveals.forEach((el) => el.classList.add('is-visible'));
        }
    });

    function copyAddress(btn) {
        const address = document.getElementById('cafe-address')?.textContent?.trim();
        if (!address) return;
        const orig = btn.textContent;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(address).catch(() => {});
        }
        btn.textContent = 'Alamat tersalin!';
        btn.classList.add('bg-brand-900', 'text-white');
        setTimeout(() => {
            btn.textContent = orig;
            btn.classList.remove('bg-brand-900', 'text-white');
        }, 1800);
    }

    function openMenuPosterModal() {
        const modal = document.getElementById('menu-poster-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeMenuPosterModal() {
        const modal = document.getElementById('menu-poster-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenuPosterModal();
        }
    });
</script>
</body>
</html>
