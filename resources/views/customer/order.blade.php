<x-layouts.app>
    <div x-data="customerQrApp()" class="w-full min-h-screen bg-slate-50 flex flex-col justify-between max-w-md mx-auto shadow-2xl relative">
        
        <!-- Top App Bar -->
        <header class="bg-[#154734] text-white p-5 rounded-b-3xl shadow-lg sticky top-0 z-30">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-[#1e5942] border border-emerald-500/30 flex items-center justify-center text-emerald-300">
                        <i data-lucide="coffee" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-extrabold tracking-tight">Cafetaria De Barrio</h1>
                        <p class="text-xs text-emerald-200">Pesan dari Meja (Self QR)</p>
                    </div>
                </div>

                <!-- Table Pill Badge -->
                <div class="px-3.5 py-1.5 rounded-full bg-emerald-800/80 border border-emerald-500/40 text-xs font-bold text-white flex items-center space-x-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-300"></i>
                    <span x-text="tableNumber"></span>
                </div>
            </div>

            <!-- Customer Name & Notes Preview -->
            <div class="mt-4 pt-3 border-t border-emerald-700/50 space-y-2">
                <input type="text" x-model="customerName" placeholder="Nama Anda (Contoh: Bima)"
                       class="w-full text-xs py-2 px-3 rounded-xl bg-[#103828] border border-emerald-600/40 text-white placeholder-emerald-300/60 focus:outline-none focus:ring-1 focus:ring-emerald-400">
            </div>
        </header>

        <!-- Category Filter Horizontal Scroll -->
        <div class="px-4 py-3 bg-white border-b border-slate-100 flex items-center space-x-2 overflow-x-auto sticky top-[138px] z-20 shadow-xs">
            <button @click="selectedCategory = 'semua'"
                    :class="selectedCategory === 'semua' ? 'bg-[#154734] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="px-4 py-1.5 rounded-full text-xs font-bold transition-all shrink-0">
                Semua
            </button>
            @foreach($categories as $cat)
                <button @click="selectedCategory = '{{ $cat->id }}'"
                        :class="selectedCategory === '{{ $cat->id }}' ? 'bg-[#154734] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-1.5 rounded-full text-xs font-bold transition-all shrink-0">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <!-- Menu Items List -->
        <main class="flex-1 p-4 pb-28 space-y-3">
            <template x-for="item in filteredMenuItems" :key="item.id">
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
                    <div class="flex-1 pr-3">
                        <h3 class="font-bold text-slate-900 text-sm" x-text="item.name"></h3>
                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-2" x-text="item.description"></p>
                        <div class="text-sm font-extrabold text-[#154734] mt-2" x-text="formatRupiah(item.price)"></div>
                    </div>

                    <!-- Quantity Stepper -->
                    <div class="shrink-0 flex items-center space-x-2">
                        <template x-if="getItemQty(item.id) > 0">
                            <div class="flex items-center space-x-2 bg-emerald-50 p-1 rounded-xl border border-emerald-200">
                                <button @click="decreaseQty(item.id)" class="w-7 h-7 rounded-lg bg-white shadow-xs text-emerald-800 font-bold flex items-center justify-center hover:bg-emerald-100 transition-colors">-</button>
                                <span class="w-5 text-center font-bold text-xs text-emerald-950" x-text="getItemQty(item.id)"></span>
                                <button @click="increaseQty(item)" class="w-7 h-7 rounded-lg bg-[#154734] text-white font-bold flex items-center justify-center hover:bg-[#0f3828] transition-colors">+</button>
                            </div>
                        </template>

                        <template x-if="getItemQty(item.id) === 0">
                            <button @click="increaseQty(item)" 
                                    class="px-3.5 py-2 rounded-xl bg-[#154734] hover:bg-[#0f3828] text-white text-xs font-bold flex items-center space-x-1 shadow-sm transition-transform active:scale-95">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>Tambah</span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>
        </main>

        <!-- Customer Floating Cart Bottom Sheet -->
        <footer class="fixed bottom-0 inset-x-0 max-w-md mx-auto p-4 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-2xl z-30">
            <template x-if="cart.length > 0">
                <div class="space-y-3">
                    <!-- Customer Notes Input (Figma Note Style) -->
                    <div>
                        <input type="text" x-model="customerNotes" 
                               placeholder="Catatan pesanan: (misal: Gula sedikit, es banyak...)"
                               class="w-full text-xs py-2 px-3 rounded-xl border border-amber-300 bg-amber-50/60 placeholder-amber-700/50 text-amber-950 focus:ring-2 focus:ring-[#154734]">
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-slate-400 font-medium" x-text="cartTotalQty + ' item dipilih'"></span>
                            <div class="text-lg font-black text-slate-900" x-text="formatRupiah(cartTotalAmount)"></div>
                        </div>

                        <button @click="submitOrder()" 
                                :disabled="isSubmitting"
                                class="px-6 py-3 rounded-2xl bg-[#154734] hover:bg-[#0f3828] disabled:bg-slate-300 text-white font-bold text-sm shadow-md transition-all flex items-center space-x-2">
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Pesan Sekarang'"></span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="cart.length === 0">
                <div class="py-2 text-center text-xs text-slate-400 flex items-center justify-center space-x-2">
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-slate-400"></i>
                    <span>Pilih menu di atas untuk mulai memesan</span>
                </div>
            </template>
        </footer>

        <!-- Success Dialog -->
        <div x-show="showSuccessModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-6 max-w-xs w-full text-center space-y-4 shadow-2xl">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="check-circle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">Pesanan Diterima!</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pesanan Anda dengan kode <strong class="text-slate-800" x-text="lastOrderCode"></strong> telah dikirim ke Kasir Cafetaria De Barrio. Mohon tunggu pesanan Anda disiapkan.
                </p>
                <button @click="showSuccessModal = false" class="w-full py-2.5 rounded-xl bg-[#154734] text-white text-xs font-bold hover:bg-[#0f3828]">
                    Pesan Menu Lain
                </button>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function customerQrApp() {
            return {
                tableNumber: '{{ $tableNumber }}',
                customerName: 'Bima',
                customerNotes: 'Gula sedikit, karna perasaan bisa berubah tapi kopi harus tetap manis.',
                categories: @json($categories),
                menuItems: @json($categories->pluck('menuItems')->flatten()),
                selectedCategory: 'semua',
                cart: [],
                isSubmitting: false,
                showSuccessModal: false,
                lastOrderCode: '',

                init() {
                    // Prepopulate with sample items from Figma: Kopi Susu Gula Aren (2x) and Pisang Goreng (1x)
                    const kopi = this.menuItems.find(m => m.name.includes('Kopi Susu Gula Aren'));
                    const pisang = this.menuItems.find(m => m.name.includes('Pisang Goreng'));
                    if (kopi) {
                        this.cart.push({ id: kopi.id, name: kopi.name, price: kopi.price, quantity: 2 });
                    }
                    if (pisang) {
                        this.cart.push({ id: pisang.id, name: pisang.name, price: pisang.price, quantity: 1 });
                    }

                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                get filteredMenuItems() {
                    if (this.selectedCategory === 'semua') return this.menuItems;
                    return this.menuItems.filter(m => String(m.category_id) === String(this.selectedCategory));
                },

                get cartTotalQty() {
                    return this.cart.reduce((sum, i) => sum + i.quantity, 0);
                },

                get cartTotalAmount() {
                    return this.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
                },

                getItemQty(menuId) {
                    const item = this.cart.find(i => i.id === menuId);
                    return item ? item.quantity : 0;
                },

                increaseQty(menu) {
                    const existing = this.cart.find(i => i.id === menu.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.cart.push({ id: menu.id, name: menu.name, price: menu.price, quantity: 1 });
                    }
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                },

                decreaseQty(menuId) {
                    const index = this.cart.findIndex(i => i.id === menuId);
                    if (index !== -1) {
                        if (this.cart[index].quantity > 1) {
                            this.cart[index].quantity--;
                        } else {
                            this.cart.splice(index, 1);
                        }
                    }
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                },

                async submitOrder() {
                    if (this.cart.length === 0) return;
                    this.isSubmitting = true;

                    try {
                        const res = await fetch('{{ route("orders.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                table_number: this.tableNumber,
                                customer_name: this.customerName || 'Pelanggan ' + this.tableNumber,
                                customer_notes: this.customerNotes,
                                source: 'QR Meja',
                                items: this.cart.map(i => ({
                                    menu_item_id: i.id,
                                    quantity: i.quantity
                                }))
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.lastOrderCode = data.order.order_code;
                            this.cart = [];
                            this.showSuccessModal = true;
                            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                        } else {
                            alert(data.message || 'Gagal mengirim pesanan');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan mengirim pesanan.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                formatRupiah(num) {
                    if (!num) return 'Rp 0';
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>
