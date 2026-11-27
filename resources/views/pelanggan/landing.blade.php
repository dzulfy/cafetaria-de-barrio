<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Warkop De Barrio — Meja {{ $tableNumber ?? '05' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECEAE4] font-sans text-ink antialiased flex justify-center min-h-screen">
@php
    $tableNumber = $tableNumber ?? session('active_table', '05');
    $isTableInactive = $isTableInactive ?? false;
    $favoriteMenus = $favoriteMenus ?? collect();
    $minPrice = $minPrice ?? 6000;
    $cartCount = $cartCount ?? 0;
@endphp

<div class="w-full max-w-[420px] min-h-screen bg-cream-50 shadow-xl flex flex-col">
    {{-- Top Bar --}}
    <header class="h-16 px-5 bg-cream-50 flex items-center justify-between shrink-0">
        <a href="{{ route('landing', ['table' => $tableNumber]) }}" class="flex items-center gap-2.5">
            <div class="size-9 rounded-full bg-brand-900 text-cream flex items-center justify-center shadow-2xs">
                <x-lucide.coffee class="size-5" />
            </div>
            <span class="font-bold text-[17px] text-ink tracking-tight">De Barrio</span>
        </a>

        @auth
            @if (auth()->user()->hasRole('pelanggan'))
                <a href="{{ route('pelanggan.akun') }}"
                   class="rounded-[10px] border border-line bg-white px-3.5 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:bg-brand-100">
                    Akun
                </a>
            @else
                <a href="{{ route(auth()->user()->homeRoute()) }}"
                   class="rounded-[10px] border border-line bg-white px-3.5 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:bg-brand-100">
                    Panel {{ ucfirst(auth()->user()->peran ?? auth()->user()->role) }}
                </a>
            @endif
        @else
            <a href="{{ route('login') }}"
               class="rounded-[10px] border border-line bg-white px-3.5 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:bg-brand-100">
                Masuk
            </a>
        @endauth
    </header>

    {{-- Hero Section (Figma 2:34) --}}
    <section class="bg-brand-900 px-5 pt-7 pb-7 text-white">
        <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-700/90 px-3 py-1.5 text-xs font-medium text-white">
            <x-lucide.qr-code class="size-3.5 shrink-0" />
            <span>Kamu memesan dari Meja {{ $tableNumber }}</span>
        </div>

        @if ($isTableInactive)
            <div class="mt-3 rounded-xl bg-danger-bg px-3.5 py-2.5 text-xs font-semibold text-danger border border-danger/20">
                Meja ini sedang nonaktif. Silakan hubungi kasir.
            </div>
        @endif

        <h1 class="mt-3.5 text-[28px] font-extrabold leading-[1.2] tracking-tight text-white">
            Ngopi manual brew,<br>harga anak kos.
        </h1>

        <p class="mt-3 text-[13px] leading-[1.6] text-[#D7E5DC]">
            Warkop Cafetaria De Barrio tempat nongkrong, ngerjain tugas, dan ngopi tanpa bikin dompet menjerit.
        </p>

        <div class="mt-5 grid grid-cols-2 gap-2.5">
            <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
               class="h-11 rounded-xl bg-cream text-brand-900 font-semibold text-[13px] flex items-center justify-center gap-2 shadow-xs transition hover:bg-[#efe6ca]">
                <x-lucide.coffee class="size-[18px]" />
                <span>Lihat Menu</span>
            </a>

            <a href="{{ route('cart', ['table' => $tableNumber]) }}"
               class="h-11 rounded-xl bg-brand-700 text-white font-semibold text-[13px] flex items-center justify-center gap-2 border border-white/10 transition hover:bg-brand-700/80 relative">
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
        </div>
    </section>

    {{-- Info Cards (Figma 2:53) --}}
    <section class="px-5 pt-5 grid grid-cols-3 gap-2.5">
        <div class="bg-white border border-line rounded-2xl p-3 flex flex-col gap-1.5 shadow-2xs">
            <svg class="size-[18px] text-brand-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
            <span class="text-[11px] font-medium text-muted">Lokasi</span>
            <span class="text-xs font-bold text-ink leading-snug">Telukjambe Timur, Karawang</span>
        </div>

        <div class="bg-white border border-line rounded-2xl p-3 flex flex-col gap-1.5 shadow-2xs">
            <x-lucide.clock class="size-[18px] text-brand-900" />
            <span class="text-[11px] font-medium text-muted">Jam buka</span>
            <span class="text-xs font-bold text-ink leading-snug">10.00 - 23.00</span>
        </div>

        <div class="bg-white border border-line rounded-2xl p-3 flex flex-col gap-1.5 shadow-2xs">
            <x-lucide.wallet class="size-[18px] text-brand-900" />
            <span class="text-[11px] font-medium text-muted">Harga</span>
            <span class="text-xs font-bold text-ink leading-snug">Mulai Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
        </div>
    </section>

    {{-- Menu Favorit (Figma 2:73) --}}
    <section class="px-5 pt-6">
        <div class="flex items-center justify-between">
            <h2 class="text-[15px] font-bold text-ink">Menu Favorit</h2>
            <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
               class="text-xs font-semibold text-brand-900 hover:underline">
                Lihat semua
            </a>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-3">
            @forelse ($favoriteMenus as $fav)
                <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
                   class="bg-white border border-line rounded-2xl p-2.5 flex flex-col shadow-2xs transition hover:border-brand-900/40">
                    <div class="h-24 rounded-xl bg-cream flex items-center justify-center text-gold overflow-hidden mb-2.5">
                        @if ($fav->image)
                            <img src="{{ asset('storage/' . $fav->image) }}" alt="{{ $fav->name }}" class="w-full h-full object-cover">
                        @else
                            <x-lucide.coffee class="size-8" />
                        @endif
                    </div>
                    <h3 class="text-[13px] font-bold text-ink leading-tight truncate">{{ $fav->name }}</h3>
                    <p class="text-[13px] font-bold text-brand-900 mt-1">{{ $fav->formatted_price }}</p>
                </a>
            @empty
                <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
                   class="bg-white border border-line rounded-2xl p-2.5 flex flex-col shadow-2xs">
                    <div class="h-24 rounded-xl bg-cream flex items-center justify-center text-gold mb-2.5">
                        <x-lucide.coffee class="size-8" />
                    </div>
                    <h3 class="text-[13px] font-bold text-ink leading-tight truncate">Kopi Susu Gula Aren</h3>
                    <p class="text-[13px] font-bold text-brand-900 mt-1">Rp 16.000</p>
                </a>
                <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
                   class="bg-white border border-line rounded-2xl p-2.5 flex flex-col shadow-2xs">
                    <div class="h-24 rounded-xl bg-cream flex items-center justify-center text-gold mb-2.5">
                        <x-lucide.coffee class="size-8" />
                    </div>
                    <h3 class="text-[13px] font-bold text-ink leading-tight truncate">V60 Manual Brew</h3>
                    <p class="text-[13px] font-bold text-brand-900 mt-1">Rp 18.000</p>
                </a>
            @endforelse
        </div>
    </section>

    {{-- Tentang Kami (Figma 2:76) --}}
    <section class="px-5 pt-6">
        <h2 class="text-[15px] font-bold text-ink">Tentang Kami</h2>
        <p class="mt-2 text-[13px] leading-[1.6] text-muted">
            Warkop dengan penyajian kopi manual brew. Harga dijaga tetap murah, mayoritas di bawah Rp 20.000, cocok untuk mahasiswa dan anak kos.
        </p>
    </section>

    {{-- Lokasi (Figma 2:83) --}}
    <section class="px-5 pt-6 pb-7">
        <h2 class="text-[15px] font-bold text-ink">Lokasi</h2>
        <div class="mt-2.5 h-[120px] rounded-2xl bg-brand-100 flex items-center justify-center text-brand-900">
            <svg class="size-9" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
        </div>
        <p class="mt-2.5 text-xs leading-relaxed text-muted">
            Jl. HS. Ronggo Waluyo No. 4, Desa Krajan, Telukjambe Timur, Kab. Karawang
        </p>
    </section>

    {{-- Footer (Figma 2:85) --}}
    <footer class="mt-auto bg-brand-900 py-4 px-5 text-center text-[11px] font-medium text-[#D7E5DC]">
        &copy; 2026 Warkop Cafetaria De Barrio
    </footer>
</div>
</body>
</html>
