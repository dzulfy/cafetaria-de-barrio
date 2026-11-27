<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $menu->name }} — De Barrio</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-de-barrio.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="bg-cream-50 text-ink antialiased min-h-screen flex flex-col">
@php
    $tableNumber = $tableNumber ?? null;
    $tableParams = $tableNumber ? ['table' => $tableNumber] : [];
    $activeTables = $activeTables ?? collect();
    $cart = $cart ?? [];
    $isTableInactive = $isTableInactive ?? false;
    $addOnMenus = $addOnMenus ?? collect();
    $relatedMenus = $relatedMenus ?? collect();

    $totalCartItems = (int) array_sum(array_column($cart, 'quantity'));
    $totalCartPrice = 0;
    foreach ($cart as $cItem) {
        $totalCartPrice += ((int) ($cItem['price'] ?? 0)) * ((int) ($cItem['quantity'] ?? 0));
    }

    $currentItemQty = (int) ($cart[(string) $menu->id]['quantity'] ?? 0);
    $menuCategory = $menuCategory ?? $menu->getRelationValue('category');
    $categorySlug = $menuCategory?->slug ?? ($menu->getAttributes()['category'] ?? 'minuman');
    $categoryName = $menuCategory?->name ?? ucfirst((string) $categorySlug);
    $isDrink = in_array($categorySlug, ['minuman', 'kopi', 'non-kopi'], true);
    $estimasiText = in_array($categorySlug, ['minuman', 'kopi', 'non-kopi', 'add-on'], true) ? '± 5 menit' : '± 8–10 menit';
    $penyajianText = $isDrink ? 'Panas / Es' : 'Disajikan Hangat';
@endphp

<div class="w-full min-h-screen bg-cream-50 relative flex flex-col">
    {{-- Sticky Responsive Top Bar --}}
    <header class="sticky top-0 z-30 bg-cream-50/95 backdrop-blur-md border-b border-line/60">
        <div class="max-w-6xl mx-auto px-5 md:px-8 py-3.5 flex items-center gap-3">
            <a href="{{ route('customer.menu', $tableParams) }}"
               class="w-10 h-10 rounded-xl bg-white border border-line flex items-center justify-center text-ink shadow-2xs hover:bg-gray-50 hover:-translate-x-0.5 active:scale-95 transition shrink-0"
               title="Kembali ke Daftar Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>

            <div class="flex items-center gap-2.5 flex-1 min-w-0">
                <h1 class="text-lg sm:text-xl font-bold text-ink tracking-tight truncate">Detail Menu</h1>
                @if ($tableNumber)
                    <span class="inline-flex items-center gap-1 rounded-full bg-brand-100 px-2.5 py-0.5 text-[11px] font-semibold text-brand-900 shrink-0">
                        Meja {{ $tableNumber }}
                    </span>
                @elseif ($activeTables->isNotEmpty())
                    <select onchange="if (this.value) window.location.href = this.value"
                            aria-label="Pilih Meja"
                            class="rounded-full bg-white border border-line px-2.5 py-1 text-[11px] font-semibold text-ink focus:border-brand-900 focus:outline-hidden cursor-pointer">
                        <option value="">Pilih Meja</option>
                        @foreach ($activeTables as $tbl)
                            <option value="{{ route('customer.menu.show', ['menu' => $menu->id, 'table' => $tbl->table_number]) }}">
                                {{ $tbl->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <a href="{{ route('cart', $tableParams) }}"
               class="w-10 h-10 md:w-auto md:px-4 rounded-xl bg-white border border-line flex items-center justify-center gap-2 text-ink shadow-2xs hover:bg-brand-100/60 active:scale-95 transition relative shrink-0"
               title="Keranjang">
                <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="hidden md:inline text-xs font-semibold">Keranjang</span>
                <span id="cart-badge-count"
                      class="{{ $totalCartItems > 0 ? '' : 'hidden' }} absolute -top-1.5 -right-1.5 bg-brand-900 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white">
                    {{ $totalCartItems }}
                </span>
            </a>
        </div>
    </header>

    @if ($isTableInactive)
        <div class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-3">
            <div class="rounded-xl bg-danger-bg px-3.5 py-2.5 text-xs font-semibold text-danger border border-danger/20">
                Meja {{ $tableNumber }} sedang nonaktif. Silakan hubungi kasir.
            </div>
        </div>
    @endif

    <main class="max-w-6xl mx-auto w-full px-5 md:px-8 py-5 md:py-8 flex-1 pb-28 md:pb-12 space-y-8">
        {{-- Breadcrumb --}}
        <nav class="hidden sm:flex items-center gap-2 text-xs text-muted">
            <a href="{{ route('customer.menu', $tableParams) }}" class="hover:text-brand-900 font-medium transition">Menu</a>
            <span>/</span>
            <a href="{{ route('customer.menu', array_filter(['table' => $tableNumber, 'kategori' => $menuCategory?->slug])) }}" class="hover:text-brand-900 font-medium transition">{{ $categoryName }}</a>
            <span>/</span>
            <span class="text-ink font-semibold truncate">{{ $menu->name }}</span>
        </nav>

        {{-- Product Hero & Details --}}
        <section class="grid grid-cols-1 md:grid-cols-12 gap-5 md:gap-7 items-start">
            {{-- Visual Showcase --}}
            <div class="md:col-span-5 relative rounded-3xl bg-cream border border-line overflow-hidden aspect-[4/3] md:aspect-square flex items-center justify-center shadow-2xs">
                @if ($menu->image)
                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                @else
                    <div class="flex flex-col items-center justify-center gap-3 text-gold p-6 text-center">
                        @if ($isDrink)
                            <x-lucide.coffee class="size-20 sm:size-24 text-brand-900/80" />
                        @else
                            <svg class="size-20 sm:size-24 text-brand-900/80" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h18M5 13a7 7 0 0 0 14 0M8 19h8M9 4c0 1.5-1 2.2-1 3.5M12 3c0 1.5-1 2.2-1 3.5M15 4c0 1.5-1 2.2-1 3.5" />
                            </svg>
                        @endif
                        <span class="text-xs font-semibold tracking-wide uppercase text-brand-900/60">{{ $categoryName }} • De Barrio</span>
                    </div>
                @endif

                <div class="absolute top-3.5 left-3.5 flex items-center gap-2">
                    @if ($menu->is_available)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/95 backdrop-blur-xs border border-line px-3 py-1 text-[11px] font-bold text-brand-900 shadow-2xs">
                            <span class="size-2 rounded-full bg-emerald-500"></span>
                            Tersedia
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-danger-bg border border-danger/20 px-3 py-1 text-[11px] font-bold text-danger shadow-2xs">
                            Stok Habis
                        </span>
                    @endif
                </div>

                <div class="absolute bottom-3.5 right-3.5 size-10 rounded-full bg-cream overflow-hidden border border-brand-900/15 shadow-sm">
                    <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafeteria De Barrio" class="w-full h-full object-cover">
                </div>
            </div>

            {{-- Product Description & Actions --}}
            <div class="md:col-span-7 bg-white border border-line rounded-3xl p-5 sm:p-6 md:p-7 shadow-2xs space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="inline-flex items-center rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-900">
                        {{ $categoryName }}
                    </span>

                    <span id="in-cart-pill" class="{{ $currentItemQty > 0 ? '' : 'hidden' }} inline-flex items-center gap-1.5 rounded-full bg-cream px-3 py-1 text-xs font-bold text-brand-900 border border-line">
                        <span id="in-cart-qty-text">{{ $currentItemQty }}</span> di keranjang
                    </span>
                </div>

                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight leading-snug">{{ $menu->name }}</h2>
                    <p class="mt-2 text-xl sm:text-2xl font-extrabold text-brand-900">{{ $menu->formatted_price }}</p>
                </div>

                <div class="border-t border-line/70 pt-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-muted mb-1.5">Deskripsi Menu</h3>
                    <p class="text-sm sm:text-[15px] leading-relaxed text-ink/85">
                        {{ $menu->description ?? 'Menu pilihan khas Cafeteria De Barrio yang disajikan segar setiap pesanan masuk.' }}
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-2.5 sm:gap-3 pt-1">
                    <div class="rounded-2xl bg-cream-50 border border-line p-3">
                        <p class="text-[11px] font-medium text-muted">Kategori</p>
                        <p class="text-xs sm:text-sm font-bold text-ink mt-0.5 truncate">{{ $categoryName }}</p>
                    </div>
                    <div class="rounded-2xl bg-cream-50 border border-line p-3">
                        <p class="text-[11px] font-medium text-muted">Estimasi Siap</p>
                        <p class="text-xs sm:text-sm font-bold text-ink mt-0.5">{{ $estimasiText }}</p>
                    </div>
                    <div class="rounded-2xl bg-cream-50 border border-line p-3">
                        <p class="text-[11px] font-medium text-muted">Penyajian</p>
                        <p class="text-xs sm:text-sm font-bold text-ink mt-0.5 truncate">{{ $penyajianText }}</p>
                    </div>
                </div>

                {{-- Desktop / Inline Quantity & Add to Cart Controls --}}
                <div class="border-t border-line/70 pt-5 space-y-3">
                    @if (! $menu->is_available)
                        <button type="button"
                                disabled
                                class="w-full h-12 rounded-2xl bg-line text-muted font-bold text-sm cursor-not-allowed">
                            Stok Habis
                        </button>
                    @elseif ($isTableInactive)
                        <button type="button"
                                disabled
                                class="w-full h-12 rounded-2xl bg-line text-muted font-bold text-sm cursor-not-allowed">
                            Meja Nonaktif
                        </button>
                    @else
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="inline-flex items-center justify-between sm:justify-center gap-4 rounded-2xl border border-line bg-cream-50 px-4 h-12 shrink-0">
                                <button type="button"
                                        onclick="changeDetailQty(-1)"
                                        aria-label="Kurangi jumlah"
                                        class="w-8 h-8 rounded-full bg-white border border-line text-ink flex items-center justify-center hover:bg-gray-100 active:scale-90 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <span id="detail-qty-value" class="w-6 text-center font-extrabold text-base text-ink">1</span>
                                <button type="button"
                                        onclick="changeDetailQty(1)"
                                        aria-label="Tambah jumlah"
                                        class="w-8 h-8 rounded-full bg-brand-900 text-white flex items-center justify-center hover:bg-brand-700 active:scale-90 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>

                            <button type="button"
                                    id="detail-add-btn"
                                    onclick="addDetailToCart()"
                                    class="flex-1 h-12 px-5 rounded-2xl bg-brand-900 hover:bg-brand-700 text-white font-bold text-sm flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98] cursor-pointer">
                                <svg class="w-5 h-5 text-cream shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span id="detail-add-label">Tambah ke Keranjang • {{ $menu->formatted_price }}</span>
                            </button>
                        </div>

                        <div id="detail-view-cart-wrap" class="{{ $totalCartItems > 0 ? '' : 'hidden' }} flex items-center justify-between rounded-2xl bg-brand-100/70 border border-brand-900/15 px-4 py-2.5 text-xs">
                            <span class="font-semibold text-brand-900">
                                Total keranjang: <strong id="detail-cart-summary">{{ $totalCartItems }} item ({{ 'Rp '.number_format($totalCartPrice, 0, ',', '.') }})</strong>
                            </span>
                            <a href="{{ route('cart', $tableParams) }}" class="font-bold text-brand-900 underline hover:text-brand-700">
                                Buka Keranjang &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Add On Recommendations --}}
        @if ($addOnMenus->isNotEmpty())
            <section class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-ink">Tambah Topping / Add On</h3>
                        <p class="text-xs text-muted">Pelengkap favorit hanya Rp 5.000 per porsi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach ($addOnMenus as $addon)
                        <div class="bg-white border border-line rounded-2xl p-3.5 flex items-center justify-between gap-3 shadow-2xs hover:border-brand-900/30 transition">
                            <a href="{{ route('customer.menu.show', array_merge(['menu' => $addon->id], $tableParams)) }}" class="min-w-0 flex-1 group">
                                <p class="font-bold text-sm text-ink group-hover:text-brand-900 truncate">{{ $addon->name }}</p>
                                <p class="text-xs text-muted truncate">{{ $addon->description ?? 'Tambahan spesial' }}</p>
                                <p class="text-xs font-bold text-brand-900 mt-1">{{ $addon->formatted_price }}</p>
                            </a>

                            <button type="button"
                                    onclick="addAddonToCart({{ $addon->id }}, '{{ addslashes($addon->name) }}')"
                                    @disabled($isTableInactive)
                                    class="shrink-0 h-9 px-3.5 rounded-xl text-xs font-bold transition {{ $isTableInactive ? 'bg-line text-muted cursor-not-allowed' : 'bg-cream hover:bg-[#efe6ca] text-brand-900 border border-line active:scale-95 cursor-pointer' }}">
                                + Tambah
                            </button>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Related Menus in Same Category --}}
        @if ($relatedMenus->isNotEmpty())
            <section class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-ink">Menu {{ $categoryName }} Lainnya</h3>
                    <a href="{{ route('customer.menu', array_filter(['table' => $tableNumber, 'kategori' => $menuCategory?->slug])) }}"
                       class="text-xs sm:text-sm font-semibold text-brand-900 hover:underline">
                        Lihat semua &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($relatedMenus as $rel)
                        <a href="{{ route('customer.menu.show', array_merge(['menu' => $rel->id], $tableParams)) }}"
                           class="group bg-white border border-line rounded-2xl p-3 flex flex-col shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-brand-900/30">
                            <div class="h-24 sm:h-28 rounded-xl bg-cream flex items-center justify-center text-gold overflow-hidden mb-2.5">
                                @if ($rel->image)
                                    <img src="{{ asset('storage/' . $rel->image) }}" alt="{{ $rel->name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <x-lucide.coffee class="size-8 transition-transform duration-300 group-hover:scale-110" />
                                @endif
                            </div>
                            <h4 class="text-[13px] sm:text-sm font-bold text-ink leading-tight truncate">{{ $rel->name }}</h4>
                            <p class="text-[13px] sm:text-sm font-bold text-brand-900 mt-1">{{ $rel->formatted_price }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    {{-- Micro Toast Feedback --}}
    <div id="detail-toast" class="hidden fixed bottom-20 left-1/2 -translate-x-1/2 z-50 rounded-full bg-ink text-white px-4 py-2 text-xs font-semibold shadow-lg pointer-events-none"></div>

    {{-- Sticky Mobile Quick Cart Bar --}}
    <div id="floating-cart-bar" class="{{ $totalCartItems > 0 ? '' : 'hidden' }} md:hidden fixed bottom-4 inset-x-0 px-5 z-40 pointer-events-none">
        <a href="{{ route('cart', $tableParams) }}"
           class="pointer-events-auto mx-auto max-w-md w-full h-12 bg-brand-900 hover:bg-brand-700 text-white px-4 rounded-2xl shadow-xl flex items-center justify-between transition-all active:scale-[0.98]">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-cream" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span id="floating-cart-count" class="font-semibold text-[13px]">{{ $totalCartItems }} item</span>
            </div>
            <div class="flex items-center gap-2 font-bold text-sm">
                <span id="floating-cart-total">Rp {{ number_format($totalCartPrice, 0, ',', '.') }}</span>
                <svg class="w-4 h-4 text-cream" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>
    </div>
</div>

<script>
    const ACTIVE_TABLE_NUMBER = @json($tableNumber);
    const MENU_ID = @json((string) $menu->id);
    const MENU_NAME = @json($menu->name);
    const UNIT_PRICE = @json((int) $menu->price);
    let selectedQty = 1;
    let toastTimer = null;

    function formatRupiah(amount) {
        return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
    }

    function changeDetailQty(delta) {
        selectedQty = Math.max(1, Math.min(99, selectedQty + delta));
        const qtyEl = document.getElementById('detail-qty-value');
        const labelEl = document.getElementById('detail-add-label');
        if (qtyEl) qtyEl.textContent = selectedQty;
        if (labelEl) labelEl.textContent = `Tambah ke Keranjang • ${formatRupiah(UNIT_PRICE * selectedQty)}`;
    }

    function showDetailToast(message) {
        const toast = document.getElementById('detail-toast');
        if (!toast) return;
        toast.textContent = message;
        toast.classList.remove('hidden');
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.add('hidden');
        }, 1800);
    }

    function syncCartIndicators(data) {
        const badge = document.getElementById('cart-badge-count');
        const floatingBar = document.getElementById('floating-cart-bar');
        const floatingCount = document.getElementById('floating-cart-count');
        const floatingTotal = document.getElementById('floating-cart-total');
        const summaryWrap = document.getElementById('detail-view-cart-wrap');
        const summaryText = document.getElementById('detail-cart-summary');

        if ((data.totalItems ?? 0) > 0) {
            if (badge) {
                badge.textContent = data.totalItems;
                badge.classList.remove('hidden');
            }
            if (floatingCount) floatingCount.textContent = `${data.totalItems} item`;
            if (floatingTotal) floatingTotal.textContent = data.formattedSubtotal;
            if (floatingBar) floatingBar.classList.remove('hidden');
            if (summaryText) summaryText.textContent = `${data.totalItems} item (${data.formattedSubtotal})`;
            if (summaryWrap) summaryWrap.classList.remove('hidden');
        }
    }

    async function addDetailToCart() {
        const btn = document.getElementById('detail-add-btn');
        if (btn) btn.disabled = true;

        try {
            const response = await fetch("{{ route('cart.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    menu_id: MENU_ID,
                    action: 'add',
                    quantity: selectedQty,
                    table_number: ACTIVE_TABLE_NUMBER,
                }),
            });

            const data = await response.json();
            if (!data.success) {
                if (data.message) showDetailToast(data.message);
                return;
            }

            const inCartPill = document.getElementById('in-cart-pill');
            const inCartText = document.getElementById('in-cart-qty-text');
            if (inCartPill && inCartText && (data.itemQuantity ?? 0) > 0) {
                inCartText.textContent = data.itemQuantity;
                inCartPill.classList.remove('hidden');
            }

            syncCartIndicators(data);
            showDetailToast(`+${selectedQty} ${MENU_NAME} ditambahkan`);
            selectedQty = 1;
            changeDetailQty(0);
        } catch (err) {
            console.error('Gagal menambahkan ke keranjang:', err);
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    async function addAddonToCart(addonId, addonName) {
        try {
            const response = await fetch("{{ route('cart.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    menu_id: String(addonId),
                    action: 'increase',
                    table_number: ACTIVE_TABLE_NUMBER,
                }),
            });

            const data = await response.json();
            if (!data.success) {
                if (data.message) showDetailToast(data.message);
                return;
            }

            syncCartIndicators(data);
            showDetailToast(`+1 ${addonName} ditambahkan`);
        } catch (err) {
            console.error('Gagal menambahkan add on:', err);
        }
    }
</script>
</body>
</html>
