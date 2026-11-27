@php
    $inMockup = $inMockup ?? false;
    $tableNumber = $tableNumber ?? '05';
    $totalItems = $totalItems ?? 0;
    $subtotal = $subtotal ?? 0;
@endphp

<div class="flex flex-col flex-1 min-h-screen bg-cream-50" id="cart-container">
    {{-- Sticky Top Bar --}}
    <header class="sticky top-0 z-30 bg-cream-50/95 backdrop-blur-md border-b border-line/60">
        <div class="{{ $inMockup ? 'px-5' : 'max-w-5xl mx-auto px-5 md:px-8' }} py-3.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
                   class="w-10 h-10 rounded-xl bg-white border border-line flex items-center justify-center text-ink shadow-2xs hover:bg-gray-50 hover:-translate-x-0.5 active:scale-95 transition shrink-0"
                   title="Kembali ke Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-lg sm:text-xl font-bold text-ink tracking-tight">Keranjang</h1>
            </div>

            <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
               class="text-xs font-semibold text-brand-900 hover:underline transition">
                + Tambah Menu
            </a>
        </div>
    </header>

    <form action="{{ route('orders.store') }}" method="POST" id="order-form" onsubmit="handleOrderSubmit(event)" class="flex-1 flex flex-col">
        @csrf
        <input type="hidden" name="table_number" value="{{ $tableNumber }}">

        <div class="{{ $inMockup ? 'px-5 py-4 space-y-4' : 'max-w-5xl mx-auto w-full px-5 md:px-8 py-5 md:py-8' }} flex-1">
            @if (session('error'))
                <div class="mb-4 rounded-xl bg-danger-bg px-3.5 py-2.5 text-xs font-medium text-danger border border-danger/20 animate-fade-up">
                    {{ session('error') }}
                </div>
            @endif

            <div class="{{ $inMockup ? 'space-y-4' : 'grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start' }}">
                {{-- Left Column: Table Banner, Cart Items & Customer Form --}}
                <div class="{{ $inMockup ? 'space-y-4' : 'lg:col-span-7 space-y-4 animate-fade-up' }}">
                    {{-- Info Meja Banner --}}
                    <div class="bg-brand-100 text-brand-900 rounded-xl px-3.5 py-2.5 text-xs font-semibold flex items-center gap-2">
                        <x-lucide.qr-code class="w-4 h-4 shrink-0" />
                        <span>Pesan mandiri  |  Meja {{ $tableNumber }}</span>
                    </div>

                    {{-- Daftar Item Card --}}
                    <div class="bg-white border border-line rounded-2xl px-4 divide-y divide-line shadow-2xs" id="cart-items-wrapper">
                        @forelse($cart as $id => $item)
                            <div class="py-3.5 flex items-center justify-between gap-3 cart-item-card transition-all duration-200" data-menu-id="{{ $id }}" data-price="{{ $item['price'] }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-14 h-14 rounded-xl bg-cream flex items-center justify-center shrink-0 text-gold overflow-hidden">
                                        @if (! empty($item['image']))
                                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <x-lucide.coffee class="w-5 h-5" />
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <h2 class="font-bold text-ink text-[13px] sm:text-sm truncate leading-snug">{{ $item['name'] }}</h2>
                                        <p class="text-xs text-muted font-medium mt-0.5">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                {{-- Stepper --}}
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <button type="button"
                                            onclick="updateQuantity('{{ $id }}', 'decrease')"
                                            class="w-7 h-7 rounded-full border border-line bg-white flex items-center justify-center text-ink hover:bg-gray-50 transition cursor-pointer active:scale-90"
                                            title="Kurangi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="font-bold text-ink text-[13px] w-4 text-center qty-display">{{ $item['quantity'] }}</span>
                                    <button type="button"
                                            onclick="updateQuantity('{{ $id }}', 'increase')"
                                            class="w-7 h-7 rounded-full bg-brand-900 text-white flex items-center justify-center hover:bg-brand-700 transition cursor-pointer active:scale-90"
                                            title="Tambah">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center space-y-3" id="empty-cart-message">
                                <p class="text-sm font-medium text-muted">Keranjang belanja masih kosong.</p>
                                <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
                                   class="inline-flex items-center justify-center rounded-xl bg-brand-900 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-700 transition">
                                    Lihat Menu
                                </a>
                            </div>
                        @endforelse
                    </div>

                    {{-- Form Inputs --}}
                    <div class="space-y-3.5">
                        <div>
                            <label for="customer_name" class="block text-xs font-medium text-muted mb-1.5">
                                Nama pemesan (opsional)
                            </label>
                            <div class="relative bg-white border border-line rounded-xl focus-within:border-brand-900 focus-within:ring-2 focus-within:ring-brand-900/15 transition">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted">
                                    <x-lucide.user class="w-4 h-4" />
                                </div>
                                <input type="text"
                                       id="customer_name"
                                       name="customer_name"
                                       value="{{ old('customer_name', auth()->user()?->nama) }}"
                                       placeholder="Contoh: Rina"
                                       class="w-full pl-10 pr-3.5 py-2.5 text-sm text-ink placeholder:text-[#9AA59E] bg-transparent rounded-xl focus:outline-hidden">
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-medium text-muted mb-1.5">
                                Catatan untuk barista
                            </label>
                            <div class="bg-white border border-line rounded-xl focus-within:border-brand-900 focus-within:ring-2 focus-within:ring-brand-900/15 transition">
                                <input type="text"
                                       id="notes"
                                       name="notes"
                                       value="{{ old('notes') }}"
                                       placeholder="Contoh: gula sedikit"
                                       class="w-full px-3.5 py-2.5 text-sm text-ink placeholder:text-[#9AA59E] bg-transparent rounded-xl focus:outline-hidden">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Ringkasan, Info Bayar & Desktop Submit --}}
                <div class="{{ $inMockup ? 'space-y-4' : 'lg:col-span-5 lg:sticky lg:top-24 space-y-4 animate-fade-up' }}" style="animation-delay: 80ms;">
                    <div class="{{ $inMockup ? 'space-y-4' : 'lg:bg-white lg:border lg:border-line lg:rounded-2xl lg:p-5 lg:shadow-2xs space-y-4' }}">
                        <h2 class="hidden lg:block text-sm font-bold text-ink">Ringkasan Pembayaran</h2>

                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-muted font-medium">
                                <span id="subtotal-label">Subtotal (<span id="total-items-count">{{ $totalItems }}</span> item)</span>
                                <span id="subtotal-amount" class="text-ink font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-line"></div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-ink">Total</span>
                                <span class="text-[17px] font-extrabold text-brand-900" id="total-amount">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Banner Info Pembayaran --}}
                        <div class="bg-warn-bg rounded-xl px-3.5 py-3 flex items-start gap-2.5 text-xs text-warn-fg leading-relaxed">
                            <x-lucide.wallet class="w-4 h-4 mt-0.5 shrink-0" />
                            <span>Pembayaran dilakukan di kasir setelah pesanan dikirim.</span>
                        </div>

                        @if (! $inMockup)
                            <button type="submit"
                                    {{ $totalItems === 0 ? 'disabled' : '' }}
                                    class="btn-submit-order hidden lg:flex w-full h-12 rounded-xl bg-brand-900 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold text-sm shadow-md transition-all hover:-translate-y-0.5 active:scale-[0.99] items-center justify-center cursor-pointer">
                                Kirim Pesanan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile / Mockup Sticky Bottom CTA --}}
        <div class="{{ $inMockup ? '' : 'lg:hidden' }} sticky bottom-0 bg-cream-50/95 backdrop-blur-md border-t border-line/60 px-5 py-3.5 mt-auto">
            <button type="submit"
                    {{ $totalItems === 0 ? 'disabled' : '' }}
                    class="btn-submit-order w-full h-12 rounded-xl bg-brand-900 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold text-sm shadow-md transition cursor-pointer active:scale-[0.99] flex items-center justify-center">
                Kirim Pesanan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function triggerCartPop(el) {
        if (!el) return;
        el.classList.remove('animate-pop');
        void el.offsetWidth;
        el.classList.add('animate-pop');
    }

    function handleOrderSubmit(e) {
        const buttons = document.querySelectorAll('.btn-submit-order');
        buttons.forEach((btn) => {
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span>Mengirim pesanan...</span>
            `;
        });
    }

    function updateQuantity(menuId, action) {
        const card = document.querySelector(`.cart-item-card[data-menu-id="${menuId}"]`);
        if (!card) return;

        const qtyEl = card.querySelector('.qty-display');
        let currentQty = parseInt(qtyEl.innerText) || 0;

        if (action === 'increase') {
            currentQty++;
        } else if (action === 'decrease') {
            currentQty--;
        }

        if (currentQty <= 0) {
            card.classList.add('opacity-0', '-translate-x-2');
            setTimeout(() => {
                card.remove();
                recalculateTotals();
            }, 160);
        } else {
            qtyEl.innerText = currentQty;
            triggerCartPop(qtyEl);
            recalculateTotals();
        }

        fetch("{{ route('cart.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                menu_id: menuId,
                action: action
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const countEl = document.getElementById('total-items-count');
                const subEl = document.getElementById('subtotal-amount');
                const totalEl = document.getElementById('total-amount');
                if (countEl) countEl.innerText = data.totalItems;
                if (subEl) subEl.innerText = data.formattedSubtotal;
                if (totalEl) {
                    totalEl.innerText = data.formattedSubtotal;
                    triggerCartPop(totalEl);
                }
                if (data.totalItems === 0) {
                    const wrapper = document.getElementById('cart-items-wrapper');
                    if (wrapper && !document.getElementById('empty-cart-message')) {
                        wrapper.innerHTML = `
                            <div class="py-8 text-center space-y-3 animate-fade-up" id="empty-cart-message">
                                <p class="text-sm font-medium text-muted">Keranjang belanja masih kosong.</p>
                                <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
                                   class="inline-flex items-center justify-center rounded-xl bg-brand-900 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-700 transition">
                                    Lihat Menu
                                </a>
                            </div>
                        `;
                    }
                }
            }
        })
        .catch(err => console.error('Cart update error:', err));
    }

    function recalculateTotals() {
        const cards = document.querySelectorAll('.cart-item-card');
        let totalItems = 0;
        let totalAmount = 0;

        cards.forEach(card => {
            const price = parseInt(card.getAttribute('data-price')) || 0;
            const qty = parseInt(card.querySelector('.qty-display').innerText) || 0;
            totalItems += qty;
            totalAmount += price * qty;
        });

        const totalItemsEl = document.getElementById('total-items-count');
        if (totalItemsEl) totalItemsEl.innerText = totalItems;

        const formatted = 'Rp ' + totalAmount.toLocaleString('id-ID');
        const subtotalEl = document.getElementById('subtotal-amount');
        if (subtotalEl) subtotalEl.innerText = formatted;

        const totalEl = document.getElementById('total-amount');
        if (totalEl) {
            totalEl.innerText = formatted;
            triggerCartPop(totalEl);
        }

        document.querySelectorAll('.btn-submit-order').forEach((btn) => {
            btn.disabled = totalItems === 0;
        });
    }
</script>
@endpush
