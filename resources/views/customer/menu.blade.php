<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu — De Barrio</title>
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
    $tableNumber = $tableNumber ?? session('active_table', '05');
    $cart = $cart ?? session()->get('cart', []);
    $isTableInactive = $isTableInactive ?? false;
    $totalCartItems = (int) array_sum(array_column($cart, 'quantity'));
    $totalCartPrice = 0;
    foreach ($cart as $cItem) {
        $totalCartPrice += ((int) ($cItem['price'] ?? 0)) * ((int) ($cItem['quantity'] ?? 0));
    }
@endphp

<div class="w-full min-h-screen bg-cream-50 relative flex flex-col">

    {{-- Sticky Responsive Top Bar --}}
    <header class="sticky top-0 z-30 bg-cream-50/95 backdrop-blur-md border-b border-line/60">
        <div class="max-w-6xl mx-auto px-5 md:px-8 py-3.5 flex items-center gap-3">
            <a href="{{ route('landing', ['table' => $tableNumber]) }}"
               class="w-10 h-10 rounded-xl bg-white border border-line flex items-center justify-center text-ink shadow-2xs hover:bg-gray-50 hover:-translate-x-0.5 active:scale-95 transition shrink-0"
               title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>

            <div class="flex items-center gap-2.5 flex-1 min-w-0">
                <h1 class="text-lg sm:text-xl font-bold text-ink tracking-tight">Menu</h1>
                <span class="inline-flex items-center gap-1 rounded-full bg-brand-100 px-2.5 py-0.5 text-[11px] font-semibold text-brand-900 shrink-0">
                    Meja {{ $tableNumber }}
                </span>
            </div>

            <a href="{{ route('cart', ['table' => $tableNumber]) }}"
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

    {{-- Search & Category Controls Row --}}
    <div class="max-w-6xl mx-auto w-full px-5 md:px-8 pt-4 pb-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <form action="{{ route('customer.menu') }}" method="GET" class="relative w-full md:w-80 shrink-0">
            <input type="hidden" name="table" value="{{ $tableNumber }}">
            @if ($selectedCategory !== 'semua')
                <input type="hidden" name="kategori" value="{{ $selectedCategory }}">
            @endif
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted">
                <x-lucide.search class="w-4 h-4" />
            </div>
            <input type="text"
                   id="menu-search-input"
                   name="q"
                   value="{{ $search }}"
                   oninput="filterMenuCards(this.value)"
                   placeholder="Cari menu..."
                   class="w-full bg-white pl-10 pr-9 py-2.5 rounded-xl border border-line text-sm focus:outline-none focus:ring-2 focus:ring-brand-900/20 focus:border-brand-900 text-ink placeholder:text-[#9AA59E] shadow-2xs transition">
            <button type="button"
                    id="clear-search-btn"
                    onclick="clearMenuSearch()"
                    class="{{ empty($search) ? 'hidden' : '' }} absolute inset-y-0 right-0 pr-3 flex items-center text-muted hover:text-ink cursor-pointer"
                    title="Bersihkan pencarian">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </form>

        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-0.5">
            <a href="{{ route('customer.menu', ['table' => $tableNumber, 'q' => $search]) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition active:scale-95 {{ $selectedCategory === 'semua' ? 'bg-brand-900 text-white shadow-2xs' : 'bg-white text-ink border border-line hover:bg-brand-100/60' }}">
                Semua
            </a>

            @foreach ($categories as $category)
                <a href="{{ route('customer.menu', ['table' => $tableNumber, 'kategori' => $category->slug, 'q' => $search]) }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition active:scale-95 {{ $selectedCategory === $category->slug ? 'bg-brand-900 text-white shadow-2xs' : 'bg-white text-ink border border-line hover:bg-brand-100/60' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Responsive Menu Grid --}}
    <div class="max-w-6xl mx-auto w-full px-5 md:px-8 py-2 flex-1 pb-28">
        <div id="menu-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @forelse ($menus as $menu)
                @php
                    $itemQty = (int) ($cart[(string) $menu->id]['quantity'] ?? 0);
                @endphp
                <div data-menu-card
                     data-menu-name="{{ strtolower($menu->name) }}"
                     data-menu-desc="{{ strtolower($menu->description ?? '') }}"
                     style="animation-delay: {{ min($loop->index * 35, 280) }}ms;"
                     class="group bg-white rounded-2xl p-3 border border-line shadow-2xs hover:shadow-md hover:-translate-y-0.5 hover:border-brand-900/30 transition-all duration-200 flex items-center justify-between gap-3 animate-fade-up">
                    {{-- Thumbnail --}}
                    <div class="w-[72px] h-[72px] rounded-xl bg-cream flex items-center justify-center text-gold shrink-0 overflow-hidden">
                        @if ($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        @else
                            <x-lucide.coffee class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" />
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-[13px] sm:text-sm text-ink truncate leading-tight">{{ $menu->name }}</h3>
                        <p class="text-[11px] sm:text-xs text-muted truncate mt-0.5">{{ $menu->description ?? 'Nikmat dan segar' }}</p>
                        <p class="font-bold text-[13px] sm:text-sm text-brand-900 mt-1">{{ $menu->formatted_price }}</p>
                    </div>

                    {{-- Action --}}
                    <div class="shrink-0 flex items-center justify-end pr-0.5">
                        @if (! $menu->is_available)
                            <span class="px-2.5 py-1 rounded-full bg-danger-bg text-danger text-[11px] font-semibold">
                                Habis
                            </span>
                        @else
                            <div id="action-container-{{ $menu->id }}" data-menu-label="{{ $menu->name }}" class="flex items-center gap-2">
                                @if ($itemQty > 0)
                                    <button type="button"
                                            onclick="updateMenuCart({{ $menu->id }}, 'decrease')"
                                            class="w-7 h-7 rounded-full border border-line bg-white text-ink flex items-center justify-center hover:bg-gray-50 active:scale-90 transition-transform cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="w-4 text-center font-bold text-[13px] text-ink">{{ $itemQty }}</span>
                                    <button type="button"
                                            onclick="updateMenuCart({{ $menu->id }}, 'increase')"
                                            class="w-7 h-7 rounded-full bg-brand-900 text-white flex items-center justify-center hover:bg-brand-700 active:scale-90 transition-transform cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                @else
                                    <button type="button"
                                            onclick="updateMenuCart({{ $menu->id }}, 'increase')"
                                            class="w-[34px] h-[34px] rounded-full bg-brand-900 hover:bg-brand-700 text-white flex items-center justify-center shadow-2xs transition-transform active:scale-90 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-muted bg-white rounded-2xl border border-line">
                    <p class="font-semibold text-sm text-ink">Menu tidak ditemukan</p>
                    <p class="text-xs text-muted mt-1">Silakan cari dengan kata kunci lain.</p>
                </div>
            @endforelse
        </div>

        {{-- Live client-side empty state --}}
        <div id="live-search-empty" class="hidden py-14 text-center text-muted bg-white rounded-2xl border border-line mt-2 animate-fade-up">
            <p class="font-semibold text-sm text-ink">Menu tidak ditemukan</p>
            <p class="text-xs text-muted mt-1">Coba kata kunci lain atau tekan tombol bersihkan.</p>
        </div>
    </div>

    {{-- Micro Toast Feedback --}}
    <div id="menu-toast" class="hidden fixed bottom-20 left-1/2 -translate-x-1/2 z-50 rounded-full bg-ink text-white px-4 py-2 text-xs font-semibold shadow-lg pointer-events-none"></div>

    {{-- Sticky Bottom Cart Bar --}}
    <div id="floating-cart-bar" class="{{ $totalCartItems > 0 ? '' : 'hidden' }} fixed bottom-4 inset-x-0 px-5 z-40 pointer-events-none animate-slide-up">
        <a href="{{ route('cart', ['table' => $tableNumber]) }}"
           class="pointer-events-auto mx-auto max-w-md w-full h-12 bg-brand-900 hover:bg-brand-700 text-white px-4 rounded-2xl shadow-xl flex items-center justify-between transition-all hover:-translate-y-0.5 active:scale-[0.98]">
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
    let toastTimer = null;

    function triggerPop(el) {
        if (!el) return;
        el.classList.remove('animate-pop');
        void el.offsetWidth;
        el.classList.add('animate-pop');
    }

    function showMenuToast(message) {
        const toast = document.getElementById('menu-toast');
        if (!toast) return;
        toast.textContent = message;
        toast.classList.remove('hidden');
        triggerPop(toast);
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.add('hidden');
        }, 1600);
    }

    function filterMenuCards(query) {
        const q = (query || '').trim().toLowerCase();
        const clearBtn = document.getElementById('clear-search-btn');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', q === '');
        }

        const cards = document.querySelectorAll('[data-menu-card]');
        let visibleCount = 0;

        cards.forEach((card) => {
            const name = card.dataset.menuName || '';
            const desc = card.dataset.menuDesc || '';
            const match = q === '' || name.includes(q) || desc.includes(q);
            card.classList.toggle('hidden', !match);
            if (match) visibleCount++;
        });

        const emptyState = document.getElementById('live-search-empty');
        if (emptyState && cards.length > 0) {
            emptyState.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function clearMenuSearch() {
        const input = document.getElementById('menu-search-input');
        if (!input) return;
        input.value = '';
        filterMenuCards('');
        input.focus();
    }

    async function updateMenuCart(menuId, action) {
        try {
            const response = await fetch("{{ route('cart.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    menu_id: String(menuId),
                    action: action,
                }),
            });

            const data = await response.json();
            if (!data.success) {
                return;
            }

            const qty = data.itemQuantity ?? 0;
            const container = document.getElementById(`action-container-${menuId}`);
            const menuLabel = container?.dataset?.menuLabel || 'Menu';

            if (container) {
                if (qty > 0) {
                    container.innerHTML = `
                        <button type="button" onclick="updateMenuCart(${menuId}, 'decrease')" class="w-7 h-7 rounded-full border border-line bg-white text-ink flex items-center justify-center hover:bg-gray-50 active:scale-90 transition-transform cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <span class="w-4 text-center font-bold text-[13px] text-ink animate-pop">${qty}</span>
                        <button type="button" onclick="updateMenuCart(${menuId}, 'increase')" class="w-7 h-7 rounded-full bg-brand-900 text-white flex items-center justify-center hover:bg-brand-700 active:scale-90 transition-transform cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    `;
                } else {
                    container.innerHTML = `
                        <button type="button" onclick="updateMenuCart(${menuId}, 'increase')" class="w-[34px] h-[34px] rounded-full bg-brand-900 hover:bg-brand-700 text-white flex items-center justify-center shadow-2xs transition-transform active:scale-90 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    `;
                }
            }

            const badge = document.getElementById('cart-badge-count');
            const floatingBar = document.getElementById('floating-cart-bar');
            const floatingCount = document.getElementById('floating-cart-count');
            const floatingTotal = document.getElementById('floating-cart-total');

            if (data.totalItems > 0) {
                if (badge) {
                    badge.textContent = data.totalItems;
                    badge.classList.remove('hidden');
                    triggerPop(badge);
                }
                if (floatingCount) {
                    floatingCount.textContent = `${data.totalItems} item`;
                }
                if (floatingTotal) {
                    floatingTotal.textContent = data.formattedSubtotal;
                }
                if (floatingBar) {
                    floatingBar.classList.remove('hidden');
                    triggerPop(floatingBar);
                }
            } else {
                if (badge) badge.classList.add('hidden');
                if (floatingBar) floatingBar.classList.add('hidden');
            }

            showMenuToast(action === 'increase' ? `+1 ${menuLabel}` : `${menuLabel} dikurangi`);
        } catch (err) {
            console.error('Gagal memperbarui keranjang:', err);
        }
    }
</script>
</body>
</html>
