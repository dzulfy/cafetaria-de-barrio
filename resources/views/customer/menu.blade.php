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
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-[#ECEAE4] text-ink antialiased min-h-screen flex justify-center">
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

<div class="w-full max-w-[420px] bg-cream-50 min-h-screen shadow-xl relative flex flex-col">

    {{-- Top Bar (Figma 02 Menu) --}}
    <header class="px-5 pt-4 pb-3 flex items-center gap-3 sticky top-0 bg-cream-50/95 backdrop-blur-sm z-30">
        <a href="{{ route('landing', ['table' => $tableNumber]) }}"
           class="w-10 h-10 rounded-xl bg-white border border-line flex items-center justify-center text-ink shadow-2xs hover:bg-gray-50 transition-colors shrink-0"
           title="Kembali">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>

        <h1 class="text-lg font-bold text-ink tracking-tight flex-1">Menu</h1>

        <a href="{{ route('cart', ['table' => $tableNumber]) }}"
           class="w-10 h-10 rounded-xl bg-white border border-line flex items-center justify-center text-ink shadow-2xs hover:bg-gray-50 transition-colors relative shrink-0"
           title="Keranjang">
            <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span id="cart-badge-count"
                  class="{{ $totalCartItems > 0 ? '' : 'hidden' }} absolute -top-1.5 -right-1.5 bg-brand-900 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white">
                {{ $totalCartItems }}
            </span>
        </a>
    </header>

    @if ($isTableInactive)
        <div class="mx-5 mb-3 rounded-xl bg-danger-bg px-3.5 py-2.5 text-xs font-semibold text-danger border border-danger/20">
            Meja {{ $tableNumber }} sedang nonaktif. Silakan hubungi kasir.
        </div>
    @endif

    {{-- Search Bar --}}
    <div class="px-5 pb-3">
        <form action="{{ route('customer.menu') }}" method="GET" class="relative">
            <input type="hidden" name="table" value="{{ $tableNumber }}">
            @if ($selectedCategory !== 'semua')
                <input type="hidden" name="kategori" value="{{ $selectedCategory }}">
            @endif
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted">
                <x-lucide.search class="w-4 h-4" />
            </div>
            <input type="text"
                   name="q"
                   value="{{ $search }}"
                   placeholder="Cari menu..."
                   class="w-full bg-white pl-10 pr-4 py-2.5 rounded-xl border border-line text-sm focus:outline-none focus:ring-2 focus:ring-brand-900/20 focus:border-brand-900 text-ink placeholder:text-[#9AA59E] shadow-2xs">
        </form>
    </div>

    {{-- Category Filter Chips --}}
    <div class="px-5 pb-3 flex items-center gap-2 overflow-x-auto no-scrollbar">
        <a href="{{ route('customer.menu', ['table' => $tableNumber, 'q' => $search]) }}"
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === 'semua' ? 'bg-brand-900 text-white shadow-2xs' : 'bg-white text-ink border border-line hover:bg-gray-50' }}">
            Semua
        </a>

        @foreach ($categories as $category)
            <a href="{{ route('customer.menu', ['table' => $tableNumber, 'kategori' => $category->slug, 'q' => $search]) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === $category->slug ? 'bg-brand-900 text-white shadow-2xs' : 'bg-white text-ink border border-line hover:bg-gray-50' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    {{-- Menu Cards List --}}
    <div class="px-5 py-1 space-y-2.5 flex-1 pb-24">
        @forelse ($menus as $menu)
            @php
                $itemQty = (int) ($cart[(string) $menu->id]['quantity'] ?? 0);
            @endphp
            <div class="bg-white rounded-2xl p-2.5 border border-line shadow-2xs flex items-center justify-between gap-3">
                {{-- Thumbnail --}}
                <div class="w-[72px] h-[72px] rounded-xl bg-cream flex items-center justify-center text-gold flex-shrink-0 overflow-hidden">
                    @if ($menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                    @else
                        <x-lucide.coffee class="w-6 h-6" />
                    @endif
                </div>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-[13px] text-ink truncate leading-tight">{{ $menu->name }}</h3>
                    <p class="text-[11px] text-muted truncate mt-0.5">{{ $menu->description ?? 'Nikmat dan segar' }}</p>
                    <p class="font-bold text-[13px] text-brand-900 mt-1">{{ $menu->formatted_price }}</p>
                </div>

                {{-- Action --}}
                <div class="flex-shrink-0 flex items-center justify-end pr-1">
                    @if (! $menu->is_available)
                        <span class="px-2.5 py-1 rounded-full bg-danger-bg text-danger text-[11px] font-semibold">
                            Habis
                        </span>
                    @else
                        <div id="action-container-{{ $menu->id }}" class="flex items-center gap-2">
                            @if ($itemQty > 0)
                                <button type="button"
                                        onclick="updateMenuCart({{ $menu->id }}, 'decrease')"
                                        class="w-7 h-7 rounded-full border border-line bg-white text-ink flex items-center justify-center hover:bg-gray-50 active:scale-90 transition-transform">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <span class="w-4 text-center font-bold text-[13px] text-ink">{{ $itemQty }}</span>
                                <button type="button"
                                        onclick="updateMenuCart({{ $menu->id }}, 'increase')"
                                        class="w-7 h-7 rounded-full bg-brand-900 text-white flex items-center justify-center hover:bg-brand-700 active:scale-90 transition-transform">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            @else
                                <button type="button"
                                        onclick="updateMenuCart({{ $menu->id }}, 'increase')"
                                        class="w-[34px] h-[34px] rounded-full bg-brand-900 hover:bg-brand-700 text-white flex items-center justify-center shadow-2xs transition-transform active:scale-90">
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
            <div class="py-16 text-center text-muted">
                <p class="font-semibold text-sm text-ink">Menu tidak ditemukan</p>
                <p class="text-xs text-muted mt-1">Silakan cari dengan kata kunci lain.</p>
            </div>
        @endforelse
    </div>

    {{-- Sticky Bottom Cart Bar (Figma 2:195) --}}
    <div id="floating-cart-bar" class="{{ $totalCartItems > 0 ? '' : 'hidden' }} sticky bottom-4 px-5 pb-2 z-40 mt-auto">
        <a href="{{ route('cart', ['table' => $tableNumber]) }}"
           class="w-full h-12 bg-brand-900 hover:bg-brand-700 text-white px-4 rounded-2xl shadow-xl flex items-center justify-between transition-all active:scale-[0.98]">
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
            if (container) {
                if (qty > 0) {
                    container.innerHTML = `
                        <button type="button" onclick="updateMenuCart(${menuId}, 'decrease')" class="w-7 h-7 rounded-full border border-line bg-white text-ink flex items-center justify-center hover:bg-gray-50 active:scale-90 transition-transform">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <span class="w-4 text-center font-bold text-[13px] text-ink">${qty}</span>
                        <button type="button" onclick="updateMenuCart(${menuId}, 'increase')" class="w-7 h-7 rounded-full bg-brand-900 text-white flex items-center justify-center hover:bg-brand-700 active:scale-90 transition-transform">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    `;
                } else {
                    container.innerHTML = `
                        <button type="button" onclick="updateMenuCart(${menuId}, 'increase')" class="w-[34px] h-[34px] rounded-full bg-brand-900 hover:bg-brand-700 text-white flex items-center justify-center shadow-2xs transition-transform active:scale-90">
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
                }
                if (floatingCount) {
                    floatingCount.textContent = `${data.totalItems} item`;
                }
                if (floatingTotal) {
                    floatingTotal.textContent = data.formattedSubtotal;
                }
                if (floatingBar) {
                    floatingBar.classList.remove('hidden');
                }
            } else {
                if (badge) badge.classList.add('hidden');
                if (floatingBar) floatingBar.classList.add('hidden');
            }
        } catch (err) {
            console.error('Gagal memperbarui keranjang:', err);
        }
    }
</script>
</body>
</html>
