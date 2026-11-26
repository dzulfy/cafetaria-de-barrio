@php
    $inMockup = $inMockup ?? false;
@endphp

<div class="bg-[#F9F7F2] rounded-[32px] p-5 sm:p-7 shadow-xl border border-gray-200/70 w-full relative overflow-hidden transition-all" id="cart-container">
    
    <!-- Header -->
    <div class="flex items-center justify-between pb-3 border-b border-gray-200/60 mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('cart') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-2xs flex items-center justify-center text-gray-700 hover:bg-gray-50 transition" title="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight">Keranjang</h1>
        </div>

        <!-- Table Badge (Pesan Mandiri | Meja 05) -->
        <div class="inline-flex items-center gap-2 bg-[#D8EBE1] text-[#1D4939] px-3 py-1.5 rounded-xl text-xs font-semibold shadow-2xs">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <span class="hidden sm:inline">Pesan mandiri</span>
            <span class="hidden sm:inline text-[#1D4939]/40">|</span>
            <span>Meja {{ $tableNumber ?? '05' }}</span>
        </div>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" id="order-form">
        @csrf
        <input type="hidden" name="table_number" value="{{ $tableNumber ?? '05' }}">

        <!-- Layout Wrapper: 1 Column on Mobile/Mockup, 2 Columns on Desktop when not in mockup -->
        <div class="{{ $inMockup ? 'space-y-4' : 'grid grid-cols-1 lg:grid-cols-12 gap-6' }}">
            
            <!-- Left Column: Items List & Inputs -->
            <div class="{{ $inMockup ? '' : 'lg:col-span-7 space-y-4' }}">
                <!-- Item List Header -->
                <div class="flex items-center justify-between text-xs text-gray-500 font-semibold mb-2 px-1">
                    <span>Menu yang Dipesan</span>
                    <span class="text-[11px] text-gray-400 font-normal">Harga & Kuantitas</span>
                </div>

                <!-- Daftar Menu di Keranjang -->
                <div class="space-y-3" id="cart-items-wrapper">
                    @forelse($cart as $id => $item)
                        <div class="bg-white rounded-2xl p-3.5 border border-gray-100 shadow-2xs flex items-center justify-between gap-3 cart-item-card transition hover:border-[#1D4939]/20" data-menu-id="{{ $id }}" data-price="{{ $item['price'] }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Icon Thumbnail -->
                                <div class="w-12 h-12 rounded-xl bg-[#F6EFE6] flex items-center justify-center shrink-0 text-[#B58A57]">
                                    @if(($item['icon'] ?? 'coffee') === 'coffee')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21a9 9 0 0 0 9-9c0-3.5-2-6-5-7.5-1.5-.7-3.5-.7-5 0C8 6 6 8.5 6 12a9 9 0 0 0 6 9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 13c1 1.5 4 1.5 6 0" />
                                        </svg>
                                    @endif
                                </div>

                                <!-- Item Details -->
                                <div class="truncate">
                                    <h2 class="font-bold text-gray-900 text-sm truncate leading-snug">{{ $item['name'] }}</h2>
                                    <p class="text-xs text-gray-400 font-medium mt-0.5">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <!-- Quantity Stepper -->
                            <div class="flex items-center gap-2.5 shrink-0">
                                <button type="button" 
                                        onclick="updateQuantity('{{ $id }}', 'decrease')" 
                                        class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:border-gray-400 transition cursor-pointer active:scale-95" 
                                        title="Kurangi">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="font-semibold text-gray-900 text-sm w-4 text-center qty-display">{{ $item['quantity'] }}</span>
                                <button type="button" 
                                        onclick="updateQuantity('{{ $id }}', 'increase')" 
                                        class="w-7 h-7 rounded-full bg-[#1D4939] text-white flex items-center justify-center hover:bg-[#143529] transition cursor-pointer active:scale-95 shadow-2xs" 
                                        title="Tambah">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl p-6 text-center text-gray-400 text-sm border border-gray-100">
                            Keranjang belanja kosong
                        </div>
                    @endforelse
                </div>

                <!-- Form Inputs Section -->
                <div class="pt-2 space-y-3.5">
                    <!-- Form Input: Nama pemesan (opsional) -->
                    <div>
                        <label for="customer_name" class="block text-xs font-medium text-gray-500 mb-1.5">
                            Nama pemesan (opsional)
                        </label>
                        <div class="relative bg-white border border-gray-200 rounded-xl focus-within:border-[#1D4939] focus-within:ring-1 focus-within:ring-[#1D4939] transition">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   value="{{ old('customer_name') }}"
                                   placeholder="Contoh: Rina" 
                                   class="w-full pl-9 pr-3.5 py-2.5 text-sm text-gray-800 placeholder-gray-400 bg-transparent rounded-xl focus:outline-hidden">
                        </div>
                    </div>

                    <!-- Form Input: Catatan untuk barista -->
                    <div>
                        <label for="notes" class="block text-xs font-medium text-gray-500 mb-1.5">
                            Catatan untuk barista
                        </label>
                        <div class="bg-white border border-gray-200 rounded-xl focus-within:border-[#1D4939] focus-within:ring-1 focus-within:ring-[#1D4939] transition">
                            <input type="text" 
                                   id="notes" 
                                   name="notes" 
                                   value="{{ old('notes') }}"
                                   placeholder="Contoh: gula sedikit" 
                                   class="w-full px-3.5 py-2.5 text-sm text-gray-800 placeholder-gray-400 bg-transparent rounded-xl focus:outline-hidden">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Ringkasan & Checkout -->
            <div class="{{ $inMockup ? 'pt-2' : 'lg:col-span-5 lg:sticky lg:top-20 space-y-4' }}">
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-2xs space-y-3.5">
                    <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Ringkasan Pembayaran</h2>

                    <!-- Subtotal & Total -->
                    <div class="space-y-2 pt-1 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
                            <span id="subtotal-label">Subtotal (<span id="total-items-count">{{ $totalItems ?? 3 }}</span> item)</span>
                            <span id="subtotal-amount">Rp {{ number_format($subtotal ?? 42000, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-dashed border-gray-200">
                            <span class="text-sm font-bold text-gray-900">Total</span>
                            <span class="text-base sm:text-lg font-bold text-[#1D4939]" id="total-amount">Rp {{ number_format($subtotal ?? 42000, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Banner Info: Pembayaran di kasir -->
                    <div class="bg-[#FCF4DF] rounded-xl p-3 flex items-start gap-2.5 text-xs text-[#8D5B28] leading-relaxed">
                        <div class="mt-0.5 shrink-0 text-[#8D5B28]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span>Pembayaran dilakukan di kasir setelah pesanan dikirim.</span>
                    </div>

                    <!-- Button: Kirim Pesanan -->
                    <button type="submit" 
                            id="btn-submit-order"
                            class="w-full py-3.5 rounded-xl bg-[#1D4939] hover:bg-[#143529] text-white font-semibold text-sm shadow-md hover:shadow-lg transition cursor-pointer active:scale-[0.99] flex items-center justify-center gap-2">
                        <span>Kirim Pesanan</span>
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
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
            card.remove();
        } else {
            qtyEl.innerText = currentQty;
        }

        // Recalculate Subtotal locally
        recalculateTotals();

        // Sync with backend via fetch
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
                document.getElementById('total-items-count').innerText = data.totalItems;
                document.getElementById('subtotal-amount').innerText = data.formattedSubtotal;
                document.getElementById('total-amount').innerText = data.formattedSubtotal;
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
        if (totalEl) totalEl.innerText = formatted;

        const submitBtn = document.getElementById('btn-submit-order');
        if (submitBtn) {
            if (totalItems === 0) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }
</script>
@endpush
