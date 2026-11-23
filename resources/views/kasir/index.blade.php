<x-layouts.app>
    <div x-data="warkopApp()" x-init="initApp()" class="w-full h-full flex flex-row overflow-hidden bg-slate-100">
        
        <!-- ============================================================== -->
        <!-- SIDEBAR NAVIGATION (Tablet Left Rail)                           -->
        <!-- ============================================================== -->
        <aside class="w-20 md:w-24 bg-[#154734] flex flex-col items-center py-5 justify-between shrink-0 select-none shadow-xl z-20">
            <div class="flex flex-col items-center space-y-7 w-full">
                <!-- Brand Logo / Emblem -->
                <div class="w-12 h-12 rounded-2xl bg-[#1e5942] border border-emerald-500/30 flex items-center justify-center text-white shadow-md group cursor-pointer hover:scale-105 transition-transform" title="Warkop De Barrio">
                    <i data-lucide="coffee" class="w-6 h-6 text-emerald-300"></i>
                </div>

                <!-- Nav Menu Icons -->
                <nav class="flex flex-col items-center space-y-4 w-full px-2">
                    <!-- K2: Pesanan Baru -->
                    <button @click="currentView = 'baru'" 
                            :class="currentView === 'baru' ? 'bg-[#1e5942] text-white shadow-inner' : 'text-emerald-200/70 hover:text-white hover:bg-[#1a523c]'"
                            class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center transition-all relative group" 
                            title="Pesanan Baru (Kasir)">
                        <i data-lucide="plus-circle" class="w-6 h-6"></i>
                        <span class="text-[9px] font-medium mt-0.5">Baru</span>
                    </button>

                    <!-- K3: Pesanan Masuk (QR) (Active in Figma) -->
                    <button @click="currentView = 'qr'" 
                            :class="currentView === 'qr' ? 'bg-[#1e5942] text-white shadow-inner ring-2 ring-emerald-400/50' : 'text-emerald-200/70 hover:text-white hover:bg-[#1a523c]'"
                            class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center transition-all relative group" 
                            title="Pesanan Masuk QR">
                        <i data-lucide="qr-code" class="w-6 h-6"></i>
                        <span class="text-[9px] font-medium mt-0.5">Pesanan</span>
                        <template x-if="counts.menunggu > 0">
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-[#154734] animate-pulse" x-text="counts.menunggu"></span>
                        </template>
                    </button>

                    <!-- K5: Riwayat Transaksi -->
                    <button @click="currentView = 'riwayat'" 
                            :class="currentView === 'riwayat' ? 'bg-[#1e5942] text-white shadow-inner' : 'text-emerald-200/70 hover:text-white hover:bg-[#1a523c]'"
                            class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center transition-all relative group" 
                            title="Riwayat Transaksi">
                        <i data-lucide="receipt" class="w-6 h-6"></i>
                        <span class="text-[9px] font-medium mt-0.5">Riwayat</span>
                    </button>

                    <!-- K6: Sinkronisasi Offline -->
                    <button @click="syncData()" 
                            :class="isSyncing ? 'animate-spin text-emerald-300' : 'text-emerald-200/70 hover:text-white hover:bg-[#1a523c]'"
                            class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center transition-all relative group" 
                            title="Sinkronisasi Data">
                        <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                        <span class="text-[9px] font-medium mt-0.5">Sinkron</span>
                    </button>
                </nav>
            </div>

            <!-- Bottom Tools -->
            <div class="flex flex-col items-center space-y-4 w-full">
                <!-- QR Customer Link Simulation -->
                <a href="{{ route('customer.qr') }}" target="_blank" 
                   class="w-10 h-10 rounded-xl bg-emerald-800/40 hover:bg-emerald-800 text-emerald-300 hover:text-white flex items-center justify-center transition-colors"
                   title="Buka QR Simulasi Pelanggan">
                    <i data-lucide="smartphone" class="w-5 h-5"></i>
                </a>

                <!-- Exit / User Avatar -->
                <button class="w-10 h-10 rounded-xl bg-red-500/20 hover:bg-red-500/30 text-red-200 flex items-center justify-center transition-colors" title="Keluar">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </button>
            </div>
        </aside>

        <!-- ============================================================== -->
        <!-- VIEW 1: K3 KASIR - PESANAN MASUK (QR) (Main Figma Screen)      -->
        <!-- ============================================================== -->
        <main x-show="currentView === 'qr'" class="flex-1 flex flex-row overflow-hidden">
            
            <!-- LEFT COLUMN: Daftar Pesanan Masuk (Width ~380px) -->
            <section class="w-96 lg:w-[410px] bg-white border-r border-slate-200 flex flex-col h-full shrink-0 shadow-sm">
                <!-- Header -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">Pesanan Masuk</h1>
                        <p class="text-xs text-slate-400 mt-0.5">Warkop De Barrio Tablet POS</p>
                    </div>
                    <!-- Status Badge Online -->
                    <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Online</span>
                    </div>
                </div>

                <!-- Status Filter Tabs: Menunggu | Diproses | Selesai -->
                <div class="px-5 pt-3 pb-2 border-b border-slate-100 bg-slate-50/70">
                    <div class="flex items-center space-x-2">
                        <!-- Tab Menunggu -->
                        <button @click="activeTab = 'menunggu'"
                                :class="activeTab === 'menunggu' ? 'bg-[#154734] text-white shadow-sm font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                class="flex-1 py-2 px-3 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition-all">
                            <span>Menunggu</span>
                            <span :class="activeTab === 'menunggu' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-200 text-slate-700'"
                                  class="text-[10px] px-1.5 py-0.2 rounded-full font-bold" 
                                  x-text="counts.menunggu"></span>
                        </button>

                        <!-- Tab Diproses -->
                        <button @click="activeTab = 'diproses'"
                                :class="activeTab === 'diproses' ? 'bg-[#154734] text-white shadow-sm font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                class="flex-1 py-2 px-3 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition-all">
                            <span>Diproses</span>
                            <span :class="activeTab === 'diproses' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-200 text-slate-700'"
                                  class="text-[10px] px-1.5 py-0.2 rounded-full font-bold" 
                                  x-text="counts.diproses"></span>
                        </button>

                        <!-- Tab Selesai -->
                        <button @click="activeTab = 'selesai'"
                                :class="activeTab === 'selesai' ? 'bg-[#154734] text-white shadow-sm font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                class="flex-1 py-2 px-3 rounded-xl text-xs flex items-center justify-center space-x-1.5 transition-all">
                            <span>Selesai</span>
                        </button>
                    </div>
                </div>

                <!-- Scrollable Order Cards List -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <template x-for="order in filteredOrders" :key="order.id">
                        <div @click="selectOrder(order)"
                             :class="selectedOrder && selectedOrder.id === order.id ? 'ring-2 ring-[#154734] bg-emerald-50/30 border-transparent shadow-md' : 'bg-white border-slate-200 hover:border-emerald-300 hover:shadow-sm'"
                             class="p-4 rounded-2xl border transition-all cursor-pointer relative">
                            
                            <!-- Card Header: Code & Table | Status Badge -->
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-bold text-slate-800 text-sm">
                                    <span x-text="order.order_code"></span>
                                    <span class="text-slate-400 font-normal mx-1">|</span>
                                    <span x-text="order.table_number"></span>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    <template x-if="order.status === 'menunggu'">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">Menunggu</span>
                                    </template>
                                    <template x-if="order.status === 'diproses'">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">Diproses</span>
                                    </template>
                                    <template x-if="order.status === 'selesai'">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">Selesai</span>
                                    </template>
                                    <template x-if="order.status === 'dibatalkan'">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800 border border-rose-200">Dibatalkan</span>
                                    </template>
                                </div>
                            </div>

                            <!-- Card Middle: Time & Source -->
                            <div class="text-xs text-slate-400 mb-3 flex items-center space-x-2">
                                <span x-text="formatDate(order.created_at)"></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span x-text="order.source"></span>
                            </div>

                            <!-- Card Footer: Items summary & Total -->
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                                <span class="text-slate-500 font-medium" x-text="order.items ? (order.items.length + ' item') : '0 item'"></span>
                                <span class="text-sm font-bold text-slate-900" x-text="formatRupiah(order.total_amount)"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="filteredOrders.length === 0">
                        <div class="h-64 flex flex-col items-center justify-center text-slate-400 p-6 text-center">
                            <i data-lucide="inbox" class="w-12 h-12 text-slate-300 mb-3"></i>
                            <p class="font-medium text-sm text-slate-600">Tidak ada pesanan</p>
                            <p class="text-xs text-slate-400 mt-1">Belum ada pesanan dengan status ini.</p>
                        </div>
                    </template>
                </div>
            </section>

            <!-- RIGHT COLUMN: Order Detail (Exact Figma Layout) -->
            <section class="flex-1 bg-white flex flex-col h-full overflow-hidden">
                <template x-if="selectedOrder">
                    <div class="flex-1 flex flex-col h-full overflow-hidden">
                        
                        <!-- Top Order Detail Header -->
                        <div class="p-6 border-b border-slate-100 flex items-start justify-between bg-white shrink-0">
                            <div>
                                <div class="flex items-center space-x-3">
                                    <h2 class="text-2xl font-extrabold text-slate-900">
                                        <span x-text="selectedOrder.order_code"></span>
                                        <span class="text-slate-300 font-light mx-1">|</span>
                                        <span x-text="selectedOrder.table_number"></span>
                                    </h2>
                                    <!-- Badge -->
                                    <template x-if="selectedOrder.status === 'menunggu'">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">Menunggu</span>
                                    </template>
                                    <template x-if="selectedOrder.status === 'diproses'">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">Diproses</span>
                                    </template>
                                    <template x-if="selectedOrder.status === 'selesai'">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Selesai</span>
                                    </template>
                                </div>
                                <p class="text-xs text-slate-400 mt-1.5 flex items-center space-x-1.5">
                                    <span>Masuk <span x-text="timeAgo(selectedOrder.created_at)"></span></span>
                                    <span>•</span>
                                    <span>sumber: <strong class="text-slate-600 font-semibold" x-text="selectedOrder.source"></strong></span>
                                    <template x-if="selectedOrder.customer_name">
                                        <span>• Pelanggan: <strong class="text-slate-700 font-semibold" x-text="selectedOrder.customer_name"></strong></span>
                                    </template>
                                </p>
                            </div>

                            <!-- Quick Action: Print Order Ticket -->
                            <button @click="printTicket(selectedOrder)" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition-colors" title="Cetak Struk Pesanan">
                                <i data-lucide="printer" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <!-- Scrollable Items List and Notes -->
                        <div class="flex-1 overflow-y-auto p-6 space-y-6">
                            
                            <!-- Items List -->
                            <div class="space-y-3">
                                <template x-for="item in (selectedOrder.items || [])" :key="item.id">
                                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-9 h-9 rounded-lg bg-emerald-100/60 text-[#154734] font-bold text-xs flex items-center justify-center shrink-0">
                                                <span x-text="item.quantity + 'x'"></span>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-800 text-sm" x-text="item.menu_name"></h4>
                                                <p class="text-xs text-slate-400" x-text="'@ ' + formatRupiah(item.price)"></p>
                                                <template x-if="item.notes">
                                                    <p class="text-[11px] text-amber-700 mt-0.5 italic" x-text="'Catatan: ' + item.notes"></p>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="font-bold text-slate-900 text-sm" x-text="formatRupiah(item.subtotal)"></div>
                                    </div>
                                </template>
                            </div>

                            <!-- Customer Notes Card (Figma style) -->
                            <template x-if="selectedOrder.customer_notes">
                                <div class="p-4 rounded-2xl bg-[#FFFBEB] border border-[#FEF08A] text-amber-900">
                                    <div class="flex items-center space-x-2 text-xs font-bold text-amber-800 mb-1">
                                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                        <span>Catatan pelanggan:</span>
                                    </div>
                                    <p class="text-sm font-medium text-amber-950/90 leading-relaxed" x-text="selectedOrder.customer_notes"></p>
                                </div>
                            </template>

                            <!-- Payment details if already completed -->
                            <template x-if="selectedOrder.status === 'selesai' && selectedOrder.payment_method">
                                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-900">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2">Informasi Pembayaran</h4>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>Metode: <strong class="capitalize" x-text="selectedOrder.payment_method"></strong></div>
                                        <div>Dibayar: <strong x-text="formatRupiah(selectedOrder.amount_paid)"></strong></div>
                                        <div>Kembalian: <strong x-text="formatRupiah(selectedOrder.change_amount)"></strong></div>
                                        <div>Waktu Bayar: <strong x-text="formatDate(selectedOrder.paid_at)"></strong></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Bottom Action Bar (Exact Figma Layout) -->
                        <div class="p-6 border-t border-slate-100 bg-white flex items-center justify-between shrink-0 shadow-lg z-10">
                            <!-- Left: Total -->
                            <div>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total</span>
                                <div class="text-2xl font-black text-slate-900" x-text="formatRupiah(selectedOrder.total_amount)"></div>
                            </div>

                            <!-- Right: Actions Buttons -->
                            <div class="flex items-center space-x-3">
                                <!-- Tolak Button -->
                                <template x-if="selectedOrder.status === 'menunggu'">
                                    <button @click="rejectOrder(selectedOrder)"
                                            class="px-5 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-sm font-bold transition-colors">
                                        Tolak
                                    </button>
                                </template>

                                <!-- Proses Pesanan Button -->
                                <template x-if="selectedOrder.status === 'menunggu'">
                                    <button @click="processOrder(selectedOrder)"
                                            class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold transition-colors shadow-sm">
                                        Proses Pesanan
                                    </button>
                                </template>

                                <!-- Bayar Button (Triggers K4 Modal) -->
                                <template x-if="selectedOrder.status !== 'selesai' && selectedOrder.status !== 'dibatalkan'">
                                    <button @click="openPaymentModal(selectedOrder)"
                                            class="px-8 py-2.5 rounded-xl bg-[#154734] hover:bg-[#0f3828] text-white text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center space-x-2">
                                        <i data-lucide="wallet" class="w-4 h-4"></i>
                                        <span>Bayar</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- When no order selected -->
                <template x-if="!selectedOrder">
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                        <i data-lucide="coffee" class="w-16 h-16 text-slate-300 mb-4"></i>
                        <h3 class="text-base font-bold text-slate-700">Pilih Pesanan</h3>
                        <p class="text-xs text-slate-400 max-w-sm mt-1">Pilih salah satu kartu pesanan di sebelah kiri untuk melihat rincian item dan memproses pembayaran.</p>
                    </div>
                </template>
            </section>
        </main>

        <!-- ============================================================== -->
        <!-- VIEW 2: K2 PESANAN BARU (KASIR COUNTER POS)                    -->
        <!-- ============================================================== -->
        <main x-show="currentView === 'baru'" class="flex-1 flex flex-row overflow-hidden bg-slate-50">
            <!-- Left: Catalog & Menu Grid -->
            <section class="flex-1 flex flex-col h-full overflow-hidden bg-white border-r border-slate-200">
                <!-- Header -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Pesanan Baru (Kasir)</h1>
                        <p class="text-xs text-slate-400">Pilih meja dan menu pesanan langsung</p>
                    </div>
                    <!-- Table Selector -->
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-semibold text-slate-500">Pilih Meja:</span>
                        <select x-model="posOrder.table_number" class="py-1.5 px-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-800 bg-slate-50 focus:ring-2 focus:ring-[#154734]">
                            @foreach($tables as $table)
                                <option value="{{ $table->table_number }}">{{ $table->table_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Category Tabs -->
                <div class="px-5 py-3 border-b border-slate-100 flex items-center space-x-2 overflow-x-auto">
                    <button @click="selectedCategory = 'semua'"
                            :class="selectedCategory === 'semua' ? 'bg-[#154734] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors shrink-0">
                        Semua Menu
                    </button>
                    @foreach($categories as $cat)
                        <button @click="selectedCategory = '{{ $cat->id }}'"
                                :class="selectedCategory === '{{ $cat->id }}' ? 'bg-[#154734] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors shrink-0">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Menu Items Grid -->
                <div class="flex-1 overflow-y-auto p-5 grid grid-cols-2 md:grid-cols-3 gap-4">
                    <template x-for="item in filteredMenuItems" :key="item.id">
                        <div @click="addToCart(item)"
                             class="p-4 rounded-2xl border border-slate-200 hover:border-[#154734] bg-white hover:shadow-md transition-all cursor-pointer flex flex-col justify-between group">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-[#154734] flex items-center justify-center">
                                        <i data-lucide="coffee" class="w-4 h-4"></i>
                                    </span>
                                    <span class="text-xs font-bold text-emerald-800" x-text="formatRupiah(item.price)"></span>
                                </div>
                                <h4 class="font-bold text-slate-800 text-sm group-hover:text-[#154734]" x-text="item.name"></h4>
                                <p class="text-[11px] text-slate-400 mt-1 line-clamp-2" x-text="item.description"></p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>Tambah ke Pesanan</span>
                                <span class="w-6 h-6 rounded-full bg-slate-100 group-hover:bg-[#154734] group-hover:text-white flex items-center justify-center transition-colors">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- Right: POS Cart & Summary (Width ~380px) -->
            <section class="w-96 bg-white flex flex-col h-full shrink-0 shadow-sm">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="font-bold text-base text-slate-900">Rincian Pesanan Baru</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Meja: <strong class="text-slate-800" x-text="posOrder.table_number"></strong></p>
                </div>

                <!-- Customer Details Input -->
                <div class="p-4 border-b border-slate-100 space-y-2.5 bg-slate-50/50">
                    <input type="text" x-model="posOrder.customer_name" placeholder="Nama Pelanggan (opsional)"
                           class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-[#154734]">
                    <textarea x-model="posOrder.customer_notes" placeholder="Catatan pesanan..." rows="2"
                              class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-[#154734] resize-none"></textarea>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4 space-y-2.5">
                    <template x-for="(cartItem, idx) in posOrder.items" :key="idx">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex-1 pr-2">
                                <h4 class="text-xs font-bold text-slate-800" x-text="cartItem.name"></h4>
                                <span class="text-[11px] text-slate-400" x-text="formatRupiah(cartItem.price)"></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="decreaseCartQty(idx)" class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-100">-</button>
                                <span class="text-xs font-bold text-slate-800 w-4 text-center" x-text="cartItem.quantity"></span>
                                <button @click="increaseCartQty(idx)" class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-100">+</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="posOrder.items.length === 0">
                        <div class="h-40 flex flex-col items-center justify-center text-slate-400 text-center">
                            <i data-lucide="shopping-bag" class="w-8 h-8 text-slate-300 mb-2"></i>
                            <p class="text-xs">Keranjang masih kosong</p>
                        </div>
                    </template>
                </div>

                <!-- Footer Summary & Submit -->
                <div class="p-5 border-t border-slate-100 bg-white shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs text-slate-500 font-semibold">Total</span>
                        <span class="text-xl font-black text-slate-900" x-text="formatRupiah(posTotal)"></span>
                    </div>
                    <button @click="submitPosOrder()" 
                            :disabled="posOrder.items.length === 0 || isSubmitting"
                            class="w-full py-3 rounded-xl bg-[#154734] hover:bg-[#0f3828] disabled:bg-slate-200 text-white disabled:text-slate-400 font-bold text-sm shadow transition-all flex items-center justify-center space-x-2">
                        <span x-text="isSubmitting ? 'Memproses...' : 'Simpan Pesanan'"></span>
                    </button>
                </div>
            </section>
        </main>

        <!-- ============================================================== -->
        <!-- VIEW 3: K5 RIWAYAT TRANSAKSI                                   -->
        <!-- ============================================================== -->
        <main x-show="currentView === 'riwayat'" class="flex-1 flex flex-col h-full overflow-hidden bg-white">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Riwayat Transaksi</h1>
                    <p class="text-xs text-slate-400 mt-1">Daftar transaksi selesai dan tercatat di database</p>
                </div>
                <div class="flex items-center space-x-3">
                    <input type="text" x-model="searchRiwayat" placeholder="Cari kode/meja..."
                           class="py-2 px-3.5 rounded-xl border border-slate-200 text-xs w-64 focus:ring-2 focus:ring-[#154734]">
                </div>
            </div>

            <!-- Table of completed orders -->
            <div class="flex-1 overflow-y-auto p-6">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 font-semibold uppercase tracking-wider">
                            <th class="pb-3">Kode Order</th>
                            <th class="pb-3">Meja / Pelanggan</th>
                            <th class="pb-3">Item</th>
                            <th class="pb-3">Metode</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Waktu Bayar</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="h in filteredRiwayatOrders" :key="h.id">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 font-bold text-slate-900" x-text="h.order_code"></td>
                                <td class="py-3.5">
                                    <div class="font-semibold text-slate-800" x-text="h.table_number"></div>
                                    <div class="text-[11px] text-slate-400" x-text="h.customer_name || '-'"></div>
                                </td>
                                <td class="py-3.5" x-text="(h.items ? h.items.length : 0) + ' item'"></td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                          :class="h.payment_method === 'tunai' ? 'bg-amber-100 text-amber-800' : (h.payment_method === 'qris' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800')"
                                          x-text="h.payment_method || 'selesai'"></span>
                                </td>
                                <td class="py-3.5 font-bold text-slate-900" x-text="formatRupiah(h.total_amount)"></td>
                                <td class="py-3.5 text-slate-500" x-text="formatDate(h.paid_at || h.updated_at)"></td>
                                <td class="py-3.5 text-right">
                                    <button @click="printTicket(h)" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600" title="Cetak Ulang">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- ============================================================== -->
        <!-- MODAL K4: PEMBAYARAN (Exact Figma Pop-up Modal)                -->
        <!-- ============================================================== -->
        <div x-show="showPaymentModal" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100" @click.away="showPaymentModal = false">
                <!-- Modal Header -->
                <div class="px-6 pt-6 pb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Pembayaran</h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <span x-text="paymentOrder ? paymentOrder.order_code : ''"></span>
                            <span class="mx-1">|</span>
                            <span x-text="paymentOrder ? paymentOrder.table_number : ''"></span>
                        </p>
                    </div>
                    <button @click="showPaymentModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="px-6 pb-6 space-y-5">
                    <!-- Total Tagihan Box (Dark Green Card from Figma) -->
                    <div class="p-5 rounded-2xl bg-[#154734] text-white flex flex-col justify-center shadow-inner">
                        <span class="text-xs font-medium text-emerald-200">Total tagihan</span>
                        <div class="text-3xl font-black mt-1 tracking-tight" x-text="paymentOrder ? formatRupiah(paymentOrder.total_amount) : 'Rp 0'"></div>
                    </div>

                    <!-- Metode Pembayaran: Tunai | QRIS | Transfer -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">Metode pembayaran</label>
                        <div class="grid grid-cols-3 gap-2.5">
                            <!-- Tunai -->
                            <button @click="paymentMethod = 'tunai'"
                                    :class="paymentMethod === 'tunai' ? 'border-2 border-[#154734] bg-emerald-50/50 text-[#154734] font-bold shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'"
                                    class="py-3 px-2 rounded-2xl flex flex-col items-center justify-center space-y-1.5 transition-all">
                                <i data-lucide="banknote" class="w-5 h-5"></i>
                                <span class="text-xs">Tunai</span>
                            </button>

                            <!-- QRIS -->
                            <button @click="paymentMethod = 'qris'"
                                    :class="paymentMethod === 'qris' ? 'border-2 border-[#154734] bg-emerald-50/50 text-[#154734] font-bold shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'"
                                    class="py-3 px-2 rounded-2xl flex flex-col items-center justify-center space-y-1.5 transition-all">
                                <i data-lucide="qr-code" class="w-5 h-5"></i>
                                <span class="text-xs">QRIS</span>
                            </button>

                            <!-- Transfer -->
                            <button @click="paymentMethod = 'transfer'"
                                    :class="paymentMethod === 'transfer' ? 'border-2 border-[#154734] bg-emerald-50/50 text-[#154734] font-bold shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'"
                                    class="py-3 px-2 rounded-2xl flex flex-col items-center justify-center space-y-1.5 transition-all">
                                <i data-lucide="building-2" class="w-5 h-5"></i>
                                <span class="text-xs">Transfer</span>
                            </button>
                        </div>
                    </div>

                    <!-- Nominal Diterima Section -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nominal diterima</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-sm font-bold text-slate-400">Rp</span>
                            <input type="number" x-model.number="amountPaid" 
                                   class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 text-base font-bold text-slate-900 focus:ring-2 focus:ring-[#154734] focus:border-transparent">
                        </div>

                        <!-- Quick Nominal Chips (Figma style) -->
                        <div class="grid grid-cols-4 gap-2 mt-2">
                            <button @click="amountPaid = paymentOrder ? paymentOrder.total_amount : 0" 
                                    class="py-1.5 px-2 rounded-xl border border-slate-200 hover:border-[#154734] bg-slate-50 hover:bg-emerald-50 text-[11px] font-semibold text-slate-700 transition-colors">
                                Uang pas
                            </button>
                            <button @click="amountPaid = 20000" 
                                    class="py-1.5 px-2 rounded-xl border border-slate-200 hover:border-[#154734] bg-slate-50 hover:bg-emerald-50 text-[11px] font-semibold text-slate-700 transition-colors">
                                Rp 20.000
                            </button>
                            <button @click="amountPaid = 50000" 
                                    class="py-1.5 px-2 rounded-xl border border-slate-200 hover:border-[#154734] bg-slate-50 hover:bg-emerald-50 text-[11px] font-semibold text-slate-700 transition-colors">
                                Rp 50.000
                            </button>
                            <button @click="amountPaid = 100000" 
                                    class="py-1.5 px-2 rounded-xl border border-slate-200 hover:border-[#154734] bg-slate-50 hover:bg-emerald-50 text-[11px] font-semibold text-slate-700 transition-colors">
                                Rp 100.000
                            </button>
                        </div>
                    </div>

                    <!-- Kembalian Box (Mint Green Box from Figma) -->
                    <div class="p-4 rounded-2xl bg-[#DCFCE7] border border-emerald-200 flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-800">Kembalian</span>
                        <span class="text-lg font-black text-emerald-900" x-text="formatRupiah(calculateKembalian())"></span>
                    </div>

                    <!-- Actions Buttons -->
                    <div class="flex items-center space-x-3 pt-2">
                        <button @click="showPaymentModal = false" 
                                class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                            Batal
                        </button>
                        <button @click="confirmPayment()" 
                                :disabled="calculateKembalian() < 0 || isProcessingPayment"
                                class="flex-1 py-3 rounded-xl bg-[#154734] hover:bg-[#0f3828] disabled:bg-slate-300 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span x-text="isProcessingPayment ? 'Memproses...' : 'Konfirmasi Pembayaran'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- PRINT RECEIPT MODAL                                            -->
        <!-- ============================================================== -->
        <div x-show="showReceiptModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border text-center space-y-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="check" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Pembayaran Berhasil!</h3>
                <p class="text-xs text-slate-500">Transaksi telah disimpan di database dan status pesanan diperbarui menjadi Selesai.</p>
                
                <div class="p-3 bg-slate-50 rounded-xl text-left text-xs font-mono border space-y-1">
                    <div class="flex justify-between"><span>No Order:</span><strong x-text="receiptData?.order_code"></strong></div>
                    <div class="flex justify-between"><span>Meja:</span><strong x-text="receiptData?.table_number"></strong></div>
                    <div class="flex justify-between"><span>Metode:</span><strong class="uppercase" x-text="receiptData?.payment_method"></strong></div>
                    <div class="flex justify-between"><span>Total:</span><strong x-text="formatRupiah(receiptData?.total_amount)"></strong></div>
                    <div class="flex justify-between"><span>Bayar:</span><strong x-text="formatRupiah(receiptData?.amount_paid)"></strong></div>
                    <div class="flex justify-between text-emerald-700"><span>Kembalian:</span><strong x-text="formatRupiah(receiptData?.change_amount)"></strong></div>
                </div>

                <div class="flex space-x-2">
                    <button @click="showReceiptModal = false" class="flex-1 py-2.5 rounded-xl border text-slate-700 text-xs font-bold hover:bg-slate-50">Tutup</button>
                    <button @click="window.print()" class="flex-1 py-2.5 rounded-xl bg-[#154734] text-white text-xs font-bold hover:bg-[#0f3828]">Cetak Struk</button>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function warkopApp() {
            return {
                currentView: 'qr', // 'qr', 'baru', 'riwayat'
                activeTab: 'menunggu', // 'menunggu', 'diproses', 'selesai'
                orders: @json($orders),
                counts: @json($counts),
                categories: @json($categories),
                menuItems: @json($categories->pluck('menuItems')->flatten()),
                selectedOrder: @json($selectedOrder),
                selectedCategory: 'semua',
                isSyncing: false,
                searchRiwayat: '',

                // POS Order State (K2)
                posOrder: {
                    table_number: 'Meja 05',
                    customer_name: '',
                    customer_notes: '',
                    items: []
                },
                isSubmitting: false,

                // Payment Modal State (K4)
                showPaymentModal: false,
                paymentOrder: null,
                paymentMethod: 'tunai',
                amountPaid: 50000,
                isProcessingPayment: false,

                // Receipt Modal
                showReceiptModal: false,
                receiptData: null,

                initApp() {
                    // Default select first available order
                    if (!this.selectedOrder && this.orders.length > 0) {
                        this.selectedOrder = this.orders[0];
                    }
                    if (this.selectedOrder) {
                        this.amountPaid = this.selectedOrder.total_amount;
                    }

                    // Auto refresh every 10 seconds to keep live with customer QR orders
                    setInterval(() => {
                        this.syncData(true);
                    }, 10000);

                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                get filteredOrders() {
                    return this.orders.filter(o => o.status === this.activeTab);
                },

                get filteredRiwayatOrders() {
                    let list = this.orders.filter(o => o.status === 'selesai' || o.status === 'dibatalkan');
                    if (this.searchRiwayat.trim()) {
                        const s = this.searchRiwayat.toLowerCase();
                        list = list.filter(o => 
                            (o.order_code && o.order_code.toLowerCase().includes(s)) ||
                            (o.table_number && o.table_number.toLowerCase().includes(s)) ||
                            (o.customer_name && o.customer_name.toLowerCase().includes(s))
                        );
                    }
                    return list;
                },

                get filteredMenuItems() {
                    if (this.selectedCategory === 'semua') {
                        return this.menuItems;
                    }
                    return this.menuItems.filter(m => String(m.category_id) === String(this.selectedCategory));
                },

                get posTotal() {
                    return this.posOrder.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                selectOrder(order) {
                    this.selectedOrder = order;
                    this.amountPaid = order.total_amount;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                addToCart(item) {
                    const existing = this.posOrder.items.find(i => i.id === item.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.posOrder.items.push({
                            id: item.id,
                            name: item.name,
                            price: item.price,
                            quantity: 1
                        });
                    }
                },

                increaseCartQty(index) {
                    this.posOrder.items[index].quantity++;
                },

                decreaseCartQty(index) {
                    if (this.posOrder.items[index].quantity > 1) {
                        this.posOrder.items[index].quantity--;
                    } else {
                        this.posOrder.items.splice(index, 1);
                    }
                },

                async submitPosOrder() {
                    if (this.posOrder.items.length === 0) return;
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
                                table_number: this.posOrder.table_number,
                                customer_name: this.posOrder.customer_name || 'Pelanggan Meja ' + this.posOrder.table_number,
                                customer_notes: this.posOrder.customer_notes,
                                source: 'Kasir',
                                items: this.posOrder.items.map(i => ({
                                    menu_item_id: i.id,
                                    quantity: i.quantity
                                }))
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            // Reset cart
                            this.posOrder.items = [];
                            this.posOrder.customer_notes = '';
                            // Refresh and switch to QR Pesanan Masuk
                            await this.syncData();
                            this.currentView = 'qr';
                            this.activeTab = 'menunggu';
                            this.selectedOrder = data.order;
                        } else {
                            alert(data.message || 'Gagal membuat pesanan');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan koneksi.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async processOrder(order) {
                    try {
                        const res = await fetch(`/orders/${order.id}/process`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.syncData();
                            this.activeTab = 'diproses';
                            this.selectedOrder = data.order;
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async rejectOrder(order) {
                    if (!confirm(`Yakin ingin menolak pesanan ${order.order_code}?`)) return;
                    try {
                        const res = await fetch(`/orders/${order.id}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.syncData();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                openPaymentModal(order) {
                    this.paymentOrder = order;
                    this.amountPaid = order.total_amount;
                    this.paymentMethod = 'tunai';
                    this.showPaymentModal = true;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                calculateKembalian() {
                    if (!this.paymentOrder) return 0;
                    return (this.amountPaid || 0) - this.paymentOrder.total_amount;
                },

                async confirmPayment() {
                    if (!this.paymentOrder || this.calculateKembalian() < 0) return;
                    this.isProcessingPayment = true;

                    try {
                        const res = await fetch(`/orders/${this.paymentOrder.id}/pay`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                payment_method: this.paymentMethod,
                                amount_paid: this.amountPaid
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.receiptData = data.order;
                            this.showPaymentModal = false;
                            this.showReceiptModal = true;
                            await this.syncData();
                            this.selectedOrder = data.order;
                            this.activeTab = 'selesai';
                        } else {
                            alert(data.message || 'Pembayaran gagal');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan pembayaran.');
                    } finally {
                        this.isProcessingPayment = false;
                    }
                },

                async syncData(silent = false) {
                    if (!silent) this.isSyncing = true;
                    try {
                        const res = await fetch('{{ route("orders.list") }}', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (data.orders) {
                            this.orders = data.orders;
                            this.counts = data.counts;
                            if (this.selectedOrder) {
                                const found = this.orders.find(o => o.id === this.selectedOrder.id);
                                if (found) this.selectedOrder = found;
                            }
                        }
                    } catch (e) {
                        console.error('Sync failed', e);
                    } finally {
                        if (!silent) {
                            setTimeout(() => { this.isSyncing = false; }, 400);
                        }
                        this.$nextTick(() => {
                            if (window.lucide) lucide.createIcons();
                        });
                    }
                },

                printTicket(order) {
                    this.receiptData = order;
                    this.showReceiptModal = true;
                },

                formatRupiah(num) {
                    if (num === null || num === undefined) return 'Rp 0';
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                },

                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    const d = new Date(dateStr);
                    const day = String(d.getDate()).padStart(2, '0');
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const hours = String(d.getHours()).padStart(2, '0');
                    const mins = String(d.getMinutes()).padStart(2, '0');
                    return `${day}-${month} ${hours}:${mins}`;
                },

                timeAgo(dateStr) {
                    if (!dateStr) return 'baru saja';
                    const diff = Math.floor((new Date() - new Date(dateStr)) / 1000);
                    if (diff < 60) return 'beberapa detik lalu';
                    const min = Math.floor(diff / 60);
                    if (min < 60) return `${min} menit lalu`;
                    const hours = Math.floor(min / 60);
                    return `${hours} jam lalu`;
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>
