<x-layouts.app>
    <div x-data="syncPageApp()" x-init="init()" class="w-full h-full flex flex-row overflow-hidden bg-[#FAF8F5]">
        
        <!-- ============================================================== -->
        <!-- SIDEBAR NAVIGATION (Tablet Left Rail)                           -->
        <!-- ============================================================== -->
        <aside class="w-20 md:w-24 bg-[#154734] flex flex-col items-center py-5 justify-between shrink-0 select-none shadow-xl z-20">
            <div class="flex flex-col items-center space-y-7 w-full">
                <!-- Brand Logo / Emblem -->
                <a href="{{ route('kasir.index') }}" class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-[#154734] shadow-md hover:scale-105 transition-transform" title="Warkop De Barrio">
                    <i data-lucide="coffee" class="w-6 h-6 stroke-[2.2]"></i>
                </a>

                <!-- Nav Menu Icons -->
                <nav class="flex flex-col items-center space-y-4 w-full px-2">
                    <!-- Kasir (POS Pesanan Masuk / Baru) -->
                    <a href="{{ route('kasir.index') }}" 
                       class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center text-emerald-200/70 hover:text-white hover:bg-[#1a523c] transition-all relative group" 
                       title="Kasir">
                        <i data-lucide="layout-grid" class="w-6 h-6"></i>
                        <span class="text-[9px] font-medium mt-0.5">Kasir</span>
                    </a>

                    <!-- Pesanan QR -->
                    <a href="{{ route('kasir.index') }}" 
                       class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center text-emerald-200/70 hover:text-white hover:bg-[#1a523c] transition-all relative group" 
                       title="Pesanan QR">
                        <i data-lucide="qr-code" class="w-6 h-6"></i>
                        <span class="text-[9px] font-medium mt-0.5">Pesanan QR</span>
                        <template x-if="ordersCount > 0">
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-[#DC2626] text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-[#154734]" x-text="ordersCount"></span>
                        </template>
                    </a>

                    <!-- Riwayat Transaksi -->
                    <a href="{{ route('kasir.riwayat') }}" 
                       class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center text-emerald-200/70 hover:text-white hover:bg-[#1a523c] transition-all relative group" 
                       title="Riwayat Transaksi">
                        <i data-lucide="receipt" class="w-6 h-6"></i>
                        <span class="text-[9px] font-medium mt-0.5">Riwayat</span>
                    </a>

                    <!-- Sinkron (ACTIVE IN FIGMA) -->
                    <div class="w-14 py-2.5 rounded-2xl bg-[#1e5942] text-white flex flex-col items-center justify-center transition-all relative shadow-inner ring-1 ring-emerald-400/40 cursor-default" 
                         title="Sinkronisasi Offline">
                        <i data-lucide="refresh-cw" :class="isSyncing ? 'animate-spin' : ''" class="w-6 h-6 text-white"></i>
                        <span class="text-[9px] font-medium mt-0.5 text-emerald-100">Sinkron</span>
                    </div>
                </nav>
            </div>

            <!-- Bottom Tools -->
            <div class="flex flex-col items-center space-y-4 w-full">
                <!-- User Avatar (KS) -->
                <div class="flex flex-col items-center group cursor-pointer" title="Kasir Shift 1">
                    <div class="w-10 h-10 rounded-full bg-[#1b5e43] border border-emerald-400/30 text-white font-bold text-xs flex items-center justify-center shadow">
                        KS
                    </div>
                    <span class="text-[9px] text-emerald-200/80 font-medium mt-1">Keluar</span>
                </div>
            </div>
        </aside>

        <!-- ============================================================== -->
        <!-- MAIN CONTENT: SINKRONISASI DATA (EXACT FIGMA LAYOUT)            -->
        <!-- ============================================================== -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto p-8 lg:p-10">
            <div class="max-w-6xl w-full mx-auto flex flex-col h-full justify-between space-y-6">
                
                <div class="space-y-6">
                    <!-- Top Bar Header: Title & Offline Badge -->
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl lg:text-3xl font-extrabold text-[#111827] tracking-tight">Sinkronisasi Data</h1>

                        <!-- Status Badge Pill: Offline | X menunggu sinkron -->
                        <div @click="toggleConnectionState()" 
                             class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-xl bg-[#FEF3C7] border border-[#FDE68A] text-[#92400E] text-xs font-semibold shadow-xs cursor-pointer hover:bg-amber-100 transition-colors"
                             title="Klik untuk simulasi Offline / Online">
                            <i data-lucide="wifi-off" class="w-4 h-4 text-[#D97706]"></i>
                            <span x-text="(stats.is_offline ? 'Offline' : 'Online') + ' | ' + stats.total_menunggu + ' menunggu sinkron'"></span>
                        </div>
                    </div>

                    <!-- Notice Warning Alert Banner -->
                    <div class="p-4 rounded-2xl bg-[#FEF3C7]/90 border border-[#FDE68A] flex items-center space-x-3.5 text-amber-900 shadow-xs">
                        <div class="w-7 h-7 rounded-lg bg-amber-200/60 flex items-center justify-center shrink-0 text-[#B45309]">
                            <i data-lucide="wifi-off" class="w-4 h-4"></i>
                        </div>
                        <p class="text-xs font-medium text-[#78350F] leading-relaxed">
                            Jaringan terputus. Pesanan tetap bisa dibuat dan disimpan di perangkat, lalu dikirim otomatis saat koneksi kembali.
                        </p>
                    </div>

                    <!-- 4 Summary Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-5">
                        <!-- Card 1: Menunggu sinkron -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
                            <span class="text-xs font-medium text-slate-500">Menunggu sinkron</span>
                            <div class="text-3xl font-black text-[#B45309] mt-3" x-text="stats.menunggu">2</div>
                        </div>

                        <!-- Card 2: Gagal, perlu dicoba ulang -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
                            <span class="text-xs font-medium text-slate-500">Gagal, perlu dicoba ulang</span>
                            <div class="text-3xl font-black text-[#DC2626] mt-3" x-text="stats.gagal">1</div>
                        </div>

                        <!-- Card 3: Tersinkron hari ini -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
                            <span class="text-xs font-medium text-slate-500">Tersinkron hari ini</span>
                            <div class="text-3xl font-black text-[#15803D] mt-3" x-text="stats.synced_today">18</div>
                        </div>

                        <!-- Card 4: Terakhir sinkron -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
                            <span class="text-xs font-medium text-slate-500">Terakhir sinkron</span>
                            <div class="text-3xl font-black text-[#111827] mt-3" x-text="stats.terakhir_sinkron">19.12</div>
                        </div>
                    </div>

                    <!-- Queue Data Table -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 bg-[#FAFBFB] text-[#064E3B] font-bold">
                                    <th class="py-3.5 px-6">ID lokal</th>
                                    <th class="py-3.5 px-6">Dibuat</th>
                                    <th class="py-3.5 px-6">Total</th>
                                    <th class="py-3.5 px-6">Keterangan</th>
                                    <th class="py-3.5 px-6 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <template x-for="item in queue" :key="item.id">
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <!-- ID lokal -->
                                        <td class="py-4 px-6 font-bold text-slate-900" x-text="item.short_id || item.local_id"></td>

                                        <!-- Dibuat -->
                                        <td class="py-4 px-6 text-slate-600" x-text="formatTime(item.created_at)"></td>

                                        <!-- Total -->
                                        <td class="py-4 px-6 font-bold text-slate-900" x-text="formatRupiah(item.total_amount)"></td>

                                        <!-- Keterangan -->
                                        <td class="py-4 px-6 text-slate-600" x-text="item.description"></td>

                                        <!-- Status Badge -->
                                        <td class="py-4 px-6 text-right">
                                            <template x-if="item.status === 'pending'">
                                                <span class="px-3.5 py-1 rounded-full text-[11px] font-bold bg-[#FEF3C7] text-[#B45309] border border-[#FDE68A]">Pending</span>
                                            </template>
                                            <template x-if="item.status === 'failed'">
                                                <span class="px-3.5 py-1 rounded-full text-[11px] font-bold bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA]">Gagal</span>
                                            </template>
                                            <template x-if="item.status === 'synced'">
                                                <span class="px-3.5 py-1 rounded-full text-[11px] font-bold bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]">Tersinkron</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>

                                <!-- Empty State when all synced -->
                                <template x-if="queue.length === 0">
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-slate-400">
                                            <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-500 mx-auto mb-2"></i>
                                            <p class="font-bold text-slate-700">Semua Data Telah Tersinkron</p>
                                            <p class="text-xs text-slate-400 mt-0.5">Tidak ada antrean pesanan offline yang menunggu.</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bottom Right Action Buttons -->
                <div class="flex items-center justify-end space-x-3.5 pt-4">
                    <!-- Button: Coba lagi yang gagal -->
                    <button @click="retryFailed()"
                            :disabled="stats.gagal === 0 || isRetrying"
                            class="px-5 py-3 rounded-xl bg-white hover:bg-slate-50 disabled:bg-slate-100 border border-slate-200 text-slate-700 disabled:text-slate-400 font-bold text-xs shadow-xs transition-colors flex items-center space-x-2">
                        <span x-text="isRetrying ? 'Mencoba...' : 'Coba lagi yang gagal'"></span>
                    </button>

                    <!-- Button: Sinkronkan Sekarang -->
                    <button @click="syncNow()"
                            :disabled="queue.length === 0 || isSyncing"
                            class="px-6 py-3 rounded-xl bg-[#154734] hover:bg-[#0f3828] disabled:bg-slate-300 text-white disabled:text-slate-500 font-bold text-xs shadow-md hover:shadow-lg transition-all flex items-center space-x-2">
                        <i data-lucide="refresh-cw" :class="isSyncing ? 'animate-spin' : ''" class="w-4 h-4 text-white"></i>
                        <span x-text="isSyncing ? 'Menyinkronkan...' : 'Sinkronkan Sekarang'"></span>
                    </button>
                </div>

            </div>
        </main>

    </div>

    @push('scripts')
    <script>
        function syncPageApp() {
            return {
                stats: @json($stats),
                queue: @json($queueItems),
                ordersCount: {{ $ordersCount }},
                isSyncing: false,
                isRetrying: false,

                init() {
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                formatRupiah(num) {
                    if (!num) return 'Rp 0';
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                },

                formatTime(dateStr) {
                    if (!dateStr) return '-';
                    const d = new Date(dateStr);
                    const hours = String(d.getHours()).padStart(2, '0');
                    const mins = String(d.getMinutes()).padStart(2, '0');
                    return `${hours}.${mins}`;
                },

                async syncNow() {
                    this.isSyncing = true;
                    try {
                        const res = await fetch('{{ route("sync.now") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.fetchStatus();
                        }
                    } catch (e) {
                        console.error('Sync failed', e);
                    } finally {
                        this.isSyncing = false;
                        this.$nextTick(() => {
                            if (window.lucide) lucide.createIcons();
                        });
                    }
                },

                async retryFailed() {
                    this.isRetrying = true;
                    try {
                        const res = await fetch('{{ route("sync.retry") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.fetchStatus();
                        }
                    } catch (e) {
                        console.error('Retry failed', e);
                    } finally {
                        this.isRetrying = false;
                        this.$nextTick(() => {
                            if (window.lucide) lucide.createIcons();
                        });
                    }
                },

                async toggleConnectionState() {
                    try {
                        const res = await fetch('{{ route("sync.toggle") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.stats.is_offline = data.is_offline;
                        }
                    } catch (e) {
                        console.error('Toggle failed', e);
                    }
                },

                async fetchStatus() {
                    try {
                        const res = await fetch('{{ route("sync.status") }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.stats = data.stats;
                            this.queue = data.queue;
                        }
                    } catch (e) {
                        console.error('Fetch status failed', e);
                    }
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>
