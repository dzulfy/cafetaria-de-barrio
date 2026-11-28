<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cafetaria De Barrio{{ !empty($tableNumber) ? ' — Meja ' . $tableNumber : '' }}</title>
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
    $cafeAddress = 'Jl. HS. Ronggo Waluyo No. 4, RT.022 RW.08, Desa Krajan, Sukaharja, Telukjambe Timur, Karawang, Jawa Barat';
    $mapsCoordinates = '-6.3273058,107.3115915';
    $mapsUrl = 'https://maps.app.goo.gl/vFCsqjuqdCe2CjjH8';
    $mapsDirectionsUrl = 'https://www.google.com/maps/dir/?api=1&destination='.$mapsCoordinates;
    $mapsEmbedUrl = 'https://maps.google.com/maps?q='.$mapsCoordinates.'&hl=id&z=17&ie=UTF8&iwloc=&output=embed';
    $galleryPhotos = [
        [
            'src' => asset('images/gallery/de-barrio-fasad-malam.jpg'),
            'title' => 'Suasana Malam & Teras',
            'caption' => 'Tampak depan Cafetaria De Barrio dengan lampu hangat dan area duduk santai.',
        ],
        [
            'src' => asset('images/gallery/de-barrio-nongkrong.jpg'),
            'title' => 'Nongkrong & Akustikan',
            'caption' => 'Serunya ngopi bareng teman sambil gitaran santai di malam hari.',
        ],
        [
            'src' => asset('images/gallery/de-barrio-bar-interior.jpg'),
            'title' => 'Area Bar & Duduk Dalam',
            'caption' => 'Suasana dalam ruangan yang nyaman dan ramah untuk nongkrong atau nugas.',
        ],
        [
            'src' => asset('images/gallery/de-barrio-sudut-seduh.jpg'),
            'title' => 'Sudut Racik Cafetaria De Barrio',
            'caption' => 'Pojok penyajian minuman segar, mie hangat, dan camilan favorit anak kos.',
        ],
    ];
@endphp

<div class="w-full min-h-screen bg-cream-50 flex flex-col">
    {{-- Responsive Sticky Top Bar --}}
    <header class="sticky top-0 z-40 bg-cream-50/95 backdrop-blur-md border-b border-line/60">
        <div class="max-w-6xl mx-auto h-16 px-5 md:px-8 flex items-center justify-between gap-4">
            <a href="{{ route('landing', $tableParams) }}" class="flex items-center gap-2.5 group">
                <div class="size-9 rounded-full bg-cream overflow-hidden border border-brand-900/15 flex items-center justify-center shadow-2xs transition-transform duration-300 group-hover:-rotate-6 shrink-0">
                    <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafetaria De Barrio" class="w-full h-full object-cover">
                </div>
                <span class="font-bold text-[15px] sm:text-[17px] text-ink tracking-tight">Cafetaria De Barrio</span>
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
    <section class="relative bg-brand-900 text-white overflow-hidden">
        <img src="{{ asset('images/gallery/de-barrio-fasad-malam.jpg') }}"
             alt="Suasana Malam Cafetaria De Barrio"
             class="absolute inset-0 w-full h-full object-cover object-[center_72%] opacity-20 pointer-events-none select-none">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-900 via-brand-900/92 to-brand-900/75 pointer-events-none"></div>

        <div class="relative max-w-6xl mx-auto px-5 md:px-8 py-7 md:py-14 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 animate-fade-up">
                <button type="button"
                        onclick="openQrScannerModal()"
                        class="inline-flex items-center gap-1.5 rounded-full bg-brand-700/90 hover:bg-brand-700 border border-white/15 px-3 py-1.5 text-xs font-medium text-white shadow-2xs transition active:scale-95 cursor-pointer"
                        title="Buka kamera untuk scan QR meja">
                    <x-lucide.qr-code class="size-3.5 shrink-0" />
                    @if ($tableNumber)
                        <span>Kamu memesan dari Meja {{ $tableNumber }}</span>
                    @else
                        <span>Scan QR di meja untuk memesan</span>
                    @endif
                </button>

                @if ($isTableInactive)
                    <div class="mt-3 rounded-xl bg-danger-bg px-3.5 py-2.5 text-xs font-semibold text-danger border border-danger/20">
                        Meja ini sedang nonaktif. Silakan hubungi kasir.
                    </div>
                @endif

                <h1 class="mt-3.5 text-[28px] sm:text-4xl lg:text-[42px] font-extrabold leading-[1.18] tracking-tight text-white">
                    Ngopi, Ngemil, Nugas<br>harga anak kos.
                </h1>

                <p class="mt-3 text-[13px] sm:text-sm lg:text-[15px] leading-[1.6] text-[#D7E5DC] max-w-xl">
                    Cafetaria De Barrio tempat nongkrong, ngerjain tugas, dan ngopi tanpa bikin dompet menjerit.
                </p>

                <div class="mt-6 grid grid-cols-2 gap-2.5 sm:gap-3 sm:max-w-[440px]">
                    <a href="{{ route('customer.menu', $tableParams) }}"
                       class="h-11 sm:h-[46px] px-3.5 sm:px-5 rounded-xl bg-cream hover:bg-[#efe6ca] text-brand-900 font-bold text-[13px] sm:text-sm flex items-center justify-center gap-2 whitespace-nowrap shadow-xs transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]">
                        <x-lucide.coffee class="size-[18px] shrink-0" />
                        <span>Lihat Menu</span>
                    </a>

                    <button type="button"
                            onclick="openQrScannerModal()"
                            class="h-11 sm:h-[46px] px-3.5 sm:px-5 rounded-xl bg-brand-700 hover:bg-[#2a624a] text-cream font-semibold text-[13px] sm:text-sm flex items-center justify-center gap-2 whitespace-nowrap border border-cream/35 shadow-xs transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98] cursor-pointer">
                        <x-lucide.qr-code class="size-[18px] shrink-0" />
                        <span>Scan QR Meja</span>
                    </button>

                    <a href="{{ route('cart', $tableParams) }}"
                       class="h-11 sm:h-[46px] px-3.5 sm:px-5 rounded-xl bg-white/10 hover:bg-white/18 text-white font-semibold text-[13px] sm:text-sm flex items-center justify-center gap-2 whitespace-nowrap border border-white/18 transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98] relative">
                        <svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                       download="Daftar-Menu-Cafetaria-De-Barrio.jpg"
                       class="h-11 sm:h-[46px] px-3.5 sm:px-5 rounded-xl bg-white/10 hover:bg-white/18 text-white font-semibold text-[13px] sm:text-sm flex items-center justify-center gap-2 whitespace-nowrap border border-white/18 transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]">
                        <svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        <span>Download Menu</span>
                    </a>
                </div>

                @if (! $tableNumber && $activeTables->isNotEmpty())
                    <div class="mt-5 pt-4 border-t border-white/10">
                        <p class="text-[11px] font-medium text-[#D7E5DC] mb-2.5">Atau pilih meja secara langsung:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($activeTables as $tbl)
                                <a href="{{ $tbl->qr_url }}"
                                   class="px-3 py-1.5 rounded-lg border border-white/12 bg-brand-700/75 hover:bg-cream hover:text-brand-900 hover:border-cream text-white text-xs font-semibold transition active:scale-95">
                                    {{ $tbl->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Hero Featured Photo & Ordering Guide Card --}}
            <div class="lg:col-span-5 flex lg:justify-end animate-fade-up" style="animation-delay: 120ms;">
                <div class="w-full lg:max-w-sm rounded-3xl bg-brand-700/55 border border-white/15 backdrop-blur-md overflow-hidden shadow-2xl">
                    <div class="relative h-48 sm:h-56 lg:h-52 overflow-hidden group">
                        <img src="{{ asset('images/gallery/de-barrio-fasad-malam.jpg') }}"
                             alt="Suasana Malam Cafetaria De Barrio"
                             class="w-full h-full object-cover object-[center_68%] transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
                        <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-black/45 backdrop-blur-xs border border-white/15 px-2.5 py-1 text-[11px] font-semibold text-cream">
                                <span class="size-2 rounded-full bg-emerald-400"></span>
                                Buka 10.00 – 23.00
                            </span>
                            <span class="rounded-full bg-white/15 backdrop-blur-xs px-2.5 py-1 text-[11px] font-semibold text-cream">
                                {{ $tableNumber ? 'Meja ' . $tableNumber : 'Self-Order QR' }}
                            </span>
                        </div>
                        <div class="absolute bottom-3 left-3.5 right-3.5 flex items-center gap-2.5">
                            <div class="size-9 rounded-full bg-cream overflow-hidden border border-white/40 shrink-0 shadow">
                                <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafetaria De Barrio" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-medium text-[#D7E5DC] leading-tight">Suasana Malam Cafetaria De Barrio</p>
                                <h2 class="text-sm font-bold text-white truncate">Hangat, Santai & Terjangkau</h2>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <ol class="space-y-2.5 text-xs text-[#D7E5DC]">
                            <li class="flex items-center gap-3">
                                <span class="size-6 rounded-full bg-cream text-brand-900 font-bold flex items-center justify-center shrink-0">1</span>
                                <span>Pilih kopi, minuman, mie, atau camilan favoritmu</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="size-6 rounded-full bg-cream text-brand-900 font-bold flex items-center justify-center shrink-0">2</span>
                                <span>Tambahkan catatan & kirim pesanan ke dapur</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="size-6 rounded-full bg-cream text-brand-900 font-bold flex items-center justify-center shrink-0">3</span>
                                <span>Bayar via QRIS / Transfer atau tunai di kasir</span>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Info Cards --}}
    <section class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-6 md:pt-10 grid grid-cols-3 gap-2.5 sm:gap-4 md:gap-5">
        <a href="#lokasi-kami"
           class="reveal-on-scroll bg-white border border-line rounded-2xl p-3 sm:p-4 md:p-5 flex flex-col gap-1.5 shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-brand-900/30 group">
            <svg class="size-[18px] sm:size-5 text-brand-900 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
            <span class="text-[11px] sm:text-xs font-medium text-muted">Lokasi &bull; Lihat Peta</span>
            <span class="text-xs sm:text-sm font-bold text-ink leading-snug">Telukjambe Timur, Karawang</span>
        </a>

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
                        aria-label="Perbesar Poster Daftar Menu Cafetaria De Barrio">
                    <img src="{{ asset('images/daftar-menu-de-barrio.jpg') }}"
                         alt="Poster Daftar Menu Cafetaria De Barrio"
                         class="w-full h-auto object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                    <span class="absolute bottom-2.5 right-2.5 rounded-full bg-brand-900/90 text-white text-[11px] font-semibold px-2.5 py-1 shadow">
                        Perbesar
                    </span>
                </button>
            </div>

            <div class="md:col-span-8 flex flex-col gap-3">
                <div class="inline-flex items-center gap-1.5 self-start rounded-full bg-brand-100 px-3 py-1 text-[11px] font-semibold text-brand-900">
                    <span>Menu Resmi Cafetaria De Barrio • Free Wi-Fi</span>
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

                <div class="grid grid-cols-1 sm:flex sm:flex-wrap sm:items-center gap-2.5 sm:gap-3 pt-2">
                    <a href="{{ route('customer.menu.download') }}"
                       download="Daftar-Menu-Cafetaria-De-Barrio.jpg"
                       class="h-11 px-5 rounded-xl bg-brand-900 text-white font-semibold text-[13px] sm:text-sm inline-flex items-center justify-center gap-2 whitespace-nowrap shadow-xs transition-all duration-200 hover:bg-brand-700 hover:-translate-y-0.5 active:scale-[0.98]">
                        <svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        <span>Download Menu (JPG)</span>
                    </a>
                    <button type="button"
                            onclick="openMenuPosterModal()"
                            class="h-11 px-5 rounded-xl bg-cream text-brand-900 border border-line font-semibold text-[13px] sm:text-sm inline-flex items-center justify-center gap-2 whitespace-nowrap transition-all duration-200 hover:bg-[#efe6ca] active:scale-[0.98] cursor-pointer">
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
                <h3 id="menu-poster-modal-title" class="text-sm font-bold text-ink">Daftar Menu Cafetaria De Barrio</h3>
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
                     alt="Poster Daftar Menu Cafetaria De Barrio"
                     class="max-h-[65vh] w-auto rounded-2xl border border-line shadow-sm">
            </div>
            <div class="px-5 py-3.5 border-t border-line bg-white flex items-center justify-end gap-2.5">
                <button type="button"
                        onclick="closeMenuPosterModal()"
                        class="h-10 px-4 rounded-xl border border-line text-xs font-semibold text-muted hover:text-ink transition cursor-pointer">
                    Tutup
                </button>
                <a href="{{ route('customer.menu.download') }}"
                   download="Daftar-Menu-Cafetaria-De-Barrio.jpg"
                   class="h-10 px-4 rounded-xl bg-brand-900 text-white text-xs font-semibold inline-flex items-center gap-1.5 hover:bg-brand-700 transition">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    <span>Download Menu</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Galeri De Barrio --}}
    <section id="galeri-de-barrio" class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-7 md:pt-10 reveal-on-scroll">
        <div class="flex flex-wrap items-end justify-between gap-2">
            <div>
                <h2 class="text-[15px] sm:text-lg font-bold text-ink">Galeri Cafetaria De Barrio</h2>
                <p class="text-xs sm:text-[13px] text-muted mt-0.5">Intip suasana nongkrong hangat dan sudut santai Cafetaria De Barrio.</p>
            </div>
            <span class="rounded-full bg-brand-100 px-3 py-1 text-[11px] font-semibold text-brand-900">
                4 Momen Tempat Kami
            </span>
        </div>

        <div class="mt-3.5 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            @foreach ($galleryPhotos as $idx => $photo)
                <button type="button"
                        onclick="openGalleryLightbox({{ $idx }})"
                        class="group relative aspect-[4/5] rounded-2xl overflow-hidden border border-line bg-cream shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-brand-900/30">
                    <img src="{{ $photo['src'] }}"
                         alt="{{ $photo['title'] }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 right-3 text-white">
                        <p class="text-xs sm:text-[13px] font-bold leading-snug">{{ $photo['title'] }}</p>
                        <p class="text-[10px] sm:text-[11px] text-white/80 line-clamp-2 mt-0.5">{{ $photo['caption'] }}</p>
                    </div>
                </button>
            @endforeach
        </div>
    </section>

    {{-- Modal Lightbox Galeri --}}
    <div id="gallery-lightbox-modal"
         onclick="if (event.target === this) closeGalleryLightbox()"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-xs p-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="gallery-lightbox-title">
        <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-line">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-line">
                <div>
                    <h3 id="gallery-lightbox-title" class="text-sm font-bold text-ink">Galeri Cafetaria De Barrio</h3>
                    <p id="gallery-lightbox-counter" class="text-[11px] text-muted">1 / 4</p>
                </div>
                <button type="button"
                        onclick="closeGalleryLightbox()"
                        class="size-8 rounded-full bg-cream-50 text-ink hover:bg-line/50 flex items-center justify-center transition cursor-pointer"
                        aria-label="Tutup galeri">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 bg-cream-50 flex flex-col items-center">
                <img id="gallery-lightbox-img"
                     src=""
                     alt="Foto Galeri Cafetaria De Barrio"
                     class="max-h-[60vh] w-auto rounded-2xl border border-line shadow-sm object-contain">
                <p id="gallery-lightbox-caption" class="mt-3 text-xs text-muted text-center leading-relaxed"></p>
            </div>
            <div class="px-5 py-3 border-t border-line bg-white flex items-center justify-between gap-2">
                <button type="button"
                        onclick="navigateGallery(-1)"
                        class="h-9 px-3.5 rounded-xl border border-line text-xs font-semibold text-ink hover:bg-cream-50 transition cursor-pointer">
                    &larr; Sebelumnya
                </button>
                <button type="button"
                        onclick="navigateGallery(1)"
                        class="h-9 px-3.5 rounded-xl bg-brand-900 text-white text-xs font-semibold hover:bg-brand-700 transition cursor-pointer">
                    Berikutnya &rarr;
                </button>
            </div>
        </div>
    </div>

    {{-- Tentang Kami & Lokasi --}}
    <section class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-7 md:pt-10 pb-8 md:pb-12 grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 items-stretch">
        <div class="reveal-on-scroll bg-white border border-line rounded-2xl p-4 sm:p-5 md:p-6 shadow-2xs flex flex-col justify-between gap-4">
            <div class="flex flex-col gap-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-[15px] sm:text-lg font-bold text-ink">Tentang Kami</h2>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-brand-900 bg-brand-100 px-2.5 py-1 rounded-lg">
                        <span class="size-1.5 rounded-full bg-emerald-600"></span>
                        Buka 10.00 – 23.00
                    </span>
                </div>
                <p class="text-[13px] sm:text-sm leading-[1.65] text-muted">
                    Cafetaria De Barrio menyajikan kopi manual brew, minuman segar, mie hangat, dan camilan ramah di kantong dengan harga mayoritas di bawah Rp 20.000 — pas untuk mahasiswa, anak kos, maupun nongkrong santai.
                </p>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <div class="rounded-xl bg-cream-50 border border-line p-3 flex flex-col gap-0.5">
                        <p class="text-[11px] font-medium text-muted">Harga Ramah Kos</p>
                        <p class="text-xs sm:text-[13px] font-bold text-ink">Mulai Rp {{ number_format($minPrice, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl bg-cream-50 border border-line p-3 flex flex-col gap-0.5">
                        <p class="text-[11px] font-medium text-muted">Fasilitas</p>
                        <p class="text-xs sm:text-[13px] font-bold text-ink">Free Wi-Fi &amp; Colokan</p>
                    </div>
                    <div class="rounded-xl bg-cream-50 border border-line p-3 flex flex-col gap-0.5">
                        <p class="text-[11px] font-medium text-muted">Pemesanan</p>
                        <p class="text-xs sm:text-[13px] font-bold text-ink">Self-Order QR Meja</p>
                    </div>
                    <div class="rounded-xl bg-cream-50 border border-line p-3 flex flex-col gap-0.5">
                        <p class="text-[11px] font-medium text-muted">Pembayaran</p>
                        <p class="text-xs sm:text-[13px] font-bold text-ink">QRIS / Transfer &amp; Cash</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:flex sm:flex-wrap sm:items-center gap-2.5 pt-1">
                <a href="{{ route('customer.menu', $tableParams) }}"
                   class="h-10 px-3.5 sm:px-4 inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-xl bg-brand-900 hover:bg-brand-700 text-white text-xs font-semibold shadow-2xs transition active:scale-95">
                    <x-lucide.coffee class="size-3.5 shrink-0" />
                    <span>Jelajahi Menu</span>
                </a>
                <a href="{{ route('orders.history', $tableParams) }}"
                   class="h-10 px-3.5 sm:px-4 inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-xl bg-cream hover:bg-[#efe6ca] border border-line text-brand-900 text-xs font-semibold transition active:scale-95">
                    <span>Pesanan Saya &rarr;</span>
                </a>
            </div>
        </div>

        <div id="lokasi-kami" class="scroll-mt-20 reveal-on-scroll bg-white border border-line rounded-2xl p-4 sm:p-5 md:p-6 shadow-2xs flex flex-col justify-between gap-3">
            <div class="flex flex-col gap-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-[15px] sm:text-lg font-bold text-ink">Lokasi</h2>
                    <button type="button"
                            onclick="copyAddress(this)"
                            class="text-[11px] font-semibold text-brand-900 bg-brand-100 hover:bg-brand-900 hover:text-white px-2.5 py-1 rounded-lg transition cursor-pointer active:scale-95">
                        Salin Alamat
                    </button>
                </div>
                <div class="h-[210px] sm:h-[230px] rounded-2xl bg-brand-100 border border-line overflow-hidden shadow-inner">
                    <iframe
                        src="{{ $mapsEmbedUrl }}"
                        title="Peta Lokasi Cafetaria De Barrio"
                        class="w-full h-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen></iframe>
                </div>
                <p id="cafe-address" class="text-xs sm:text-[13px] leading-relaxed text-ink font-medium">
                    {{ $cafeAddress }}
                </p>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap sm:items-center gap-2.5 pt-1">
                <a href="{{ $mapsUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="h-10 px-3.5 sm:px-4 inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-xl bg-brand-900 hover:bg-brand-700 text-white text-xs font-semibold shadow-2xs transition active:scale-95">
                    <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span>Buka di Google Maps</span>
                </a>
                <a href="{{ $mapsDirectionsUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="h-10 px-3.5 sm:px-4 inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-xl bg-cream hover:bg-[#efe6ca] border border-line text-brand-900 text-xs font-semibold transition active:scale-95">
                    <span>Petunjuk Arah &rarr;</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="mt-auto bg-brand-900 py-4 md:py-5 px-5 text-center text-[11px] sm:text-xs font-medium text-[#D7E5DC]">
        &copy; 2026 Cafetaria De Barrio
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

    const GALLERY_PHOTOS = @json($galleryPhotos);
    let currentGalleryIndex = 0;

    function renderGalleryModal() {
        const item = GALLERY_PHOTOS[currentGalleryIndex];
        if (!item) return;
        const img = document.getElementById('gallery-lightbox-img');
        const title = document.getElementById('gallery-lightbox-title');
        const caption = document.getElementById('gallery-lightbox-caption');
        const counter = document.getElementById('gallery-lightbox-counter');
        if (img) {
            img.src = item.src;
            img.alt = item.title;
        }
        if (title) title.textContent = item.title;
        if (caption) caption.textContent = item.caption;
        if (counter) counter.textContent = `${currentGalleryIndex + 1} / ${GALLERY_PHOTOS.length}`;
    }

    function openGalleryLightbox(index) {
        currentGalleryIndex = index;
        renderGalleryModal();
        const modal = document.getElementById('gallery-lightbox-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeGalleryLightbox() {
        const modal = document.getElementById('gallery-lightbox-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function navigateGallery(delta) {
        if (!GALLERY_PHOTOS.length) return;
        currentGalleryIndex = (currentGalleryIndex + delta + GALLERY_PHOTOS.length) % GALLERY_PHOTOS.length;
        renderGalleryModal();
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenuPosterModal();
            closeGalleryLightbox();
        }
        const galleryModal = document.getElementById('gallery-lightbox-modal');
        if (galleryModal && !galleryModal.classList.contains('hidden')) {
            if (event.key === 'ArrowLeft') navigateGallery(-1);
            if (event.key === 'ArrowRight') navigateGallery(1);
        }
    });
</script>
<x-qr-scanner-modal />
</body>
</html>
