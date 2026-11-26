<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>De Barrio - Menu Pesanan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        /* Hide scrollbar for category scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-[#f2efe9] text-gray-800 antialiased min-h-screen flex flex-col justify-between items-center selection:bg-emerald-200">

    <!-- Top floating quick link to Admin -->
    <div class="w-full bg-[#16372b] text-emerald-100 px-4 py-2 text-xs flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span class="font-semibold text-white">De Barrio Cafetaria</span>
            <span class="text-emerald-300/70 hidden sm:inline">• Menu Pelanggan Terkoneksi Database</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.menus.index') }}" class="inline-flex items-center gap-1.5 bg-[#254d40] hover:bg-[#2f5e4f] text-white px-3 py-1 rounded-full text-xs font-medium transition-all shadow-xs">
                <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Beralih ke Panel Admin</span>
            </a>
        </div>
    </div>

    <!-- Mobile Screen Frame (Matching 390x844 in Image 2) -->
    <div class="w-full max-w-[420px] bg-[#fbf8f2] min-h-[844px] shadow-2xl relative flex flex-col flex-1 border-x border-gray-200/60 my-0 sm:my-4 sm:rounded-[36px] overflow-hidden">
        
        <!-- Header matching Image 2 -->
        <header class="p-4 pt-6 flex items-center justify-between sticky top-0 bg-[#fbf8f2]/90 backdrop-blur-md z-30">
            <!-- Back Button -->
            <a href="{{ route('customer.menu') }}" class="w-10 h-10 rounded-2xl bg-white border border-gray-200/80 flex items-center justify-center text-gray-700 shadow-2xs hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>

            <!-- Title -->
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Menu</h1>

            <!-- Cart Icon with dynamic badge -->
            <button type="button" onclick="openCartModal()" class="w-10 h-10 rounded-2xl bg-white border border-gray-200/80 flex items-center justify-center text-gray-700 shadow-2xs hover:bg-gray-50 transition-colors relative">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span id="cart-badge-count" class="hidden absolute -top-1.5 -right-1.5 bg-[#1b3d32] text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white">
                    0
                </span>
            </button>
        </header>

        <!-- Search Bar matching Image 2 -->
        <div class="px-4 pb-3">
            <form action="{{ route('customer.menu') }}" method="GET" class="relative">
                @if($selectedCategory !== 'semua')
                    <input type="hidden" name="kategori" value="{{ $selectedCategory }}">
                @endif
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       name="q" 
                       value="{{ $search }}" 
                       placeholder="Cari menu..." 
                       class="w-full bg-white pl-10 pr-4 py-2.5 rounded-2xl border border-gray-200/80 text-sm focus:outline-none focus:ring-2 focus:ring-[#1b3d32]/20 focus:border-[#1b3d32] text-gray-800 placeholder-gray-400 shadow-2xs">
            </form>
        </div>

        <!-- Category Horizontal Filter Chips matching Image 2 -->
        <div class="px-4 py-2 flex items-center gap-2 overflow-x-auto no-scrollbar">
            <a href="{{ route('customer.menu', ['q' => $search]) }}" 
               class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === 'semua' ? 'bg-[#1b3d32] text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-200/80 hover:bg-gray-50' }}">
                Semua
            </a>

            @foreach($categories as $category)
            <a href="{{ route('customer.menu', ['kategori' => $category->slug, 'q' => $search]) }}" 
               class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === $category->slug ? 'bg-[#1b3d32] text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-200/80 hover:bg-gray-50' }}">
                {{ $category->name }}
            </a>
            @endforeach
        </div>

        <!-- Menu Cards List matching Image 2 -->
        <div class="px-4 py-3 space-y-3 flex-1 pb-28">
            @forelse($menus as $menu)
            <div class="bg-white rounded-2xl p-3.5 border border-gray-100 shadow-2xs flex items-center justify-between gap-3 transition-transform duration-200 hover:scale-[1.01]">
                <!-- Left: Thumbnail in warm beige rounded box -->
                <div class="w-16 h-16 rounded-2xl bg-[#f5ede1] flex items-center justify-center text-[#966b3b] flex-shrink-0 shadow-inner overflow-hidden">
                    @if($menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"></path>
                        </svg>
                    @endif
                </div>

                <!-- Middle: Name, Description & Price -->
                <div class="flex-1 min-w-0 pr-1">
                    <h3 class="font-bold text-sm text-gray-900 truncate leading-tight">{{ $menu->name }}</h3>
                    <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $menu->description ?? 'Nikmat dan segar' }}</p>
                    <p class="font-bold text-sm text-gray-900 mt-1">{{ $menu->formatted_price }}</p>
                </div>

                <!-- Right: Action (Counter, Add button, or Habis badge) -->
                <div class="flex-shrink-0 flex items-center justify-end">
                    @if(!$menu->is_available)
                        <!-- Habis Badge matching Image 2 -->
                        <span class="px-3 py-1 rounded-full bg-red-50 text-red-400 text-xs font-semibold tracking-wide border border-red-100/60">
                            Habis
                        </span>
                    @else
                        <!-- Counter or Add button managed dynamically by JS -->
                        <div id="action-container-{{ $menu->id }}" class="flex items-center gap-2">
                            <button type="button" 
                                    onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ $menu->image }}')"
                                    class="w-9 h-9 rounded-full bg-[#1b3d32] hover:bg-[#254d40] text-white flex items-center justify-center shadow-xs transition-transform active:scale-90">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-16 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <p class="font-medium text-gray-600 text-sm">Menu tidak ditemukan</p>
                <p class="text-xs text-gray-400 mt-1">Silakan cari dengan nama lain.</p>
            </div>
            @endforelse
        </div>

        <!-- Sticky Floating Cart Bar matching Image 2: [Cart icon 3 item] [Rp 42.000 >] -->
        <div id="floating-cart-bar" class="hidden absolute bottom-5 left-4 right-4 z-40">
            <button type="button" 
                    onclick="openCartModal()" 
                    class="w-full bg-[#1b3d32] hover:bg-[#254d40] text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center justify-between transition-all transform active:scale-[0.98]">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span id="floating-cart-count" class="font-bold text-sm tracking-wide">0 item</span>
                </div>
                <div class="flex items-center gap-1.5 font-bold text-sm">
                    <span id="floating-cart-total">Rp 0</span>
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </button>
        </div>

    </div>

    <!-- Cart / Checkout Modal Drawer -->
    <div id="cart-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" onclick="closeCartModal()"></div>
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 text-center">
            <div class="relative transform overflow-hidden rounded-t-[32px] sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md p-6 border border-gray-100">
                <!-- Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-[#1b3d32] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Keranjang Pesanan</h3>
                    </div>
                    <button type="button" onclick="closeCartModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Cart Items List -->
                <div id="cart-items-list" class="my-4 divide-y divide-gray-100 max-h-64 overflow-y-auto pr-1">
                    <!-- Populated by JS -->
                </div>

                <!-- Customer Details Form -->
                <form id="checkout-form" onsubmit="submitOrder(event)" class="space-y-3 pt-2 border-t border-gray-100">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pemesan</label>
                            <input type="text" id="order-customer-name" placeholder="Contoh: Rian" required class="w-full text-xs rounded-xl border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-[#1b3d32]/20 focus:border-[#1b3d32] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Meja</label>
                            <input type="text" id="order-table-number" placeholder="Contoh: 04" required class="w-full text-xs rounded-xl border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-[#1b3d32]/20 focus:border-[#1b3d32] outline-none">
                        </div>
                    </div>

                    <!-- Total Breakdown -->
                    <div class="bg-[#fbf8f2] rounded-2xl p-3.5 space-y-1.5 text-xs">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span id="modal-subtotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Pajak Resto (10%)</span>
                            <span id="modal-tax">Rp 0</span>
                        </div>
                        <div class="border-t border-gray-200/60 pt-1.5 flex justify-between font-bold text-gray-900 text-sm">
                            <span>Total Pembayaran</span>
                            <span id="modal-total" class="text-[#1b3d32]">Rp 0</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit-order" class="w-full bg-[#1b3d32] hover:bg-[#254d40] text-white py-3.5 rounded-2xl font-bold text-sm shadow-md transition-all active:scale-98">
                        Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Order Success Modal -->
    <div id="success-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-center shadow-2xl transition-all w-full max-w-sm p-6 border border-gray-100">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Pesanan Diterima!</h3>
                <p id="success-order-id" class="text-xs font-semibold text-emerald-800 bg-emerald-50 py-1.5 px-3 rounded-lg inline-block my-2">ID: ORD-12345</p>
                <p class="text-xs text-gray-500 mb-5">Pesanan Anda sedang dipersiapkan oleh barista kami. Silakan tunggu di meja Anda.</p>
                <button type="button" onclick="closeSuccessModal()" class="w-full bg-[#1b3d32] text-white py-3 rounded-xl text-xs font-bold hover:bg-[#254d40]">
                    Pesan Menu Lainnya
                </button>
            </div>
        </div>
    </div>

    <script>
        // Shopping cart state
        let cart = {};

        function formatRupiah(number) {
            return 'Rp ' + Number(number).toLocaleString('id-ID');
        }

        function addToCart(id, name, price, image) {
            if (!cart[id]) {
                cart[id] = { id, name, price, image, quantity: 1 };
            } else {
                cart[id].quantity += 1;
            }
            updateCartUI();
        }

        function decreaseQuantity(id) {
            if (cart[id]) {
                cart[id].quantity -= 1;
                if (cart[id].quantity <= 0) {
                    delete cart[id];
                }
            }
            updateCartUI();
        }

        function increaseQuantity(id) {
            if (cart[id]) {
                cart[id].quantity += 1;
            }
            updateCartUI();
        }

        function updateCartUI() {
            let totalItems = 0;
            let totalPrice = 0;

            for (const id in cart) {
                totalItems += cart[id].quantity;
                totalPrice += cart[id].price * cart[id].quantity;
            }

            // Update item action buttons matching Image 2:
            // When in cart: [-] count [+]
            // When not in cart: [+]
            document.querySelectorAll('[id^="action-container-"]').forEach(container => {
                const id = container.id.replace('action-container-', '');
                if (cart[id] && cart[id].quantity > 0) {
                    container.innerHTML = `
                        <button type="button" onclick="decreaseQuantity(${id})" class="w-8 h-8 rounded-full border border-gray-300 bg-white text-gray-600 flex items-center justify-center hover:bg-gray-50 active:scale-90 font-bold transition-transform">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <span class="w-5 text-center font-bold text-sm text-gray-900">${cart[id].quantity}</span>
                        <button type="button" onclick="increaseQuantity(${id})" class="w-8 h-8 rounded-full bg-[#1b3d32] text-white flex items-center justify-center hover:bg-[#254d40] active:scale-90 font-bold transition-transform">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    `;
                } else {
                    const menuItem = @json($menus->keyBy('id'));
                    if (menuItem[id]) {
                        container.innerHTML = `
                            <button type="button" 
                                    onclick="addToCart(${id}, '${menuItem[id].name.replace(/'/g, "\\'")}', ${menuItem[id].price}, '${menuItem[id].image || ''}')"
                                    class="w-9 h-9 rounded-full bg-[#1b3d32] hover:bg-[#254d40] text-white flex items-center justify-center shadow-xs transition-transform active:scale-90">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        `;
                    }
                }
            });

            // Update top cart badge
            const badge = document.getElementById('cart-badge-count');
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            // Update floating cart bar matching Image 2: [Cart icon 3 item] [Rp 42.000 >]
            const floatingBar = document.getElementById('floating-cart-bar');
            const floatingCount = document.getElementById('floating-cart-count');
            const floatingTotal = document.getElementById('floating-cart-total');

            if (totalItems > 0) {
                floatingCount.textContent = `${totalItems} item`;
                floatingTotal.textContent = formatRupiah(totalPrice);
                floatingBar.classList.remove('hidden');
            } else {
                floatingBar.classList.add('hidden');
            }

            // Update Modal items
            renderModalCart(totalPrice);
        }

        function renderModalCart(subtotal) {
            const list = document.getElementById('cart-items-list');
            if (Object.keys(cart).length === 0) {
                list.innerHTML = `
                    <div class="py-6 text-center text-gray-400 text-xs">
                        Keranjang masih kosong.
                    </div>
                `;
            } else {
                let html = '';
                for (const id in cart) {
                    const item = cart[id];
                    html += `
                        <div class="py-2.5 flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-semibold text-xs text-gray-900 truncate">${item.name}</h4>
                                <span class="text-[11px] text-gray-400">${formatRupiah(item.price)} × ${item.quantity}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="decreaseQuantity(${id})" class="w-6 h-6 rounded-full border border-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold">-</button>
                                <span class="font-bold text-xs text-gray-800">${item.quantity}</span>
                                <button type="button" onclick="increaseQuantity(${id})" class="w-6 h-6 rounded-full bg-[#1b3d32] text-white flex items-center justify-center text-xs font-bold">+</button>
                            </div>
                        </div>
                    `;
                }
                list.innerHTML = html;
            }

            const tax = Math.round(subtotal * 0.1);
            const total = subtotal + tax;

            document.getElementById('modal-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('modal-tax').textContent = formatRupiah(tax);
            document.getElementById('modal-total').textContent = formatRupiah(total);
        }

        function openCartModal() {
            if (Object.keys(cart).length === 0) {
                alert('Keranjang Anda masih kosong. Silakan pilih menu terlebih dahulu!');
                return;
            }
            document.getElementById('cart-modal').classList.remove('hidden');
        }

        function closeCartModal() {
            document.getElementById('cart-modal').classList.add('hidden');
        }

        async function submitOrder(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-order');
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            const itemsArray = Object.values(cart).map(item => ({
                id: item.id,
                quantity: item.quantity,
            }));

            try {
                const response = await fetch("{{ route('customer.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        items: itemsArray,
                        customer_name: document.getElementById('order-customer-name').value,
                        table_number: document.getElementById('order-table-number').value,
                    })
                });

                const data = await response.json();
                if (data.success) {
                    cart = {};
                    updateCartUI();
                    closeCartModal();

                    document.getElementById('success-order-id').textContent = `ID Pesanan: ${data.order_id}`;
                    document.getElementById('success-modal').classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat membuat pesanan.');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Pesan Sekarang';
            }
        }

        function closeSuccessModal() {
            document.getElementById('success-modal').classList.add('hidden');
        }

        // Initialize state
        document.addEventListener('DOMContentLoaded', () => {
            updateCartUI();
        });
    </script>
</body>
</html>
