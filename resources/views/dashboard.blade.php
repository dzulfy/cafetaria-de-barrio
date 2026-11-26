@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section (Judul, Tanggal, & Tombol Filter Periode) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[28px] font-bold text-[#1E2922] tracking-tight leading-tight">Dashboard</h1>
            <p class="text-xs sm:text-sm text-[#6F7D75] font-medium mt-0.5">
                {{ $tanggalHariIni }}
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button 
                type="button" 
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-[#E5E0D5] text-[#1E2922] text-xs sm:text-sm font-semibold shadow-xs hover:bg-[#FAF8F4] active:scale-95 transition"
            >
                <!-- Calendar Icon -->
                <svg class="w-4 h-4 text-[#184233] stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Hari ini</span>
            </button>
        </div>
    </div>

    <!-- 4 Stat Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Pendapatan hari ini -->
        <div class="bg-white rounded-2xl p-5 border border-[#EBE8E0] shadow-xs flex flex-col justify-between transition hover:shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#6F7D75] font-medium">Pendapatan hari ini</span>
                <div class="w-8 h-8 rounded-lg bg-[#EBF2EE] text-[#184233] flex items-center justify-center shrink-0">
                    <!-- Wallet Icon -->
                    <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                        <path d="M16 14h2"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#1E2922] tracking-tight leading-none">
                    Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs font-semibold text-[#184233]">
                    {{ $trendPendapatan }}
                </div>
            </div>
        </div>

        <!-- 2. Jumlah pesanan -->
        <div class="bg-white rounded-2xl p-5 border border-[#EBE8E0] shadow-xs flex flex-col justify-between transition hover:shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#6F7D75] font-medium">Jumlah pesanan</span>
                <div class="w-8 h-8 rounded-lg bg-[#EBF2EE] text-[#184233] flex items-center justify-center shrink-0">
                    <!-- Receipt / Document Icon -->
                    <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#1E2922] tracking-tight leading-none">
                    {{ $jumlahPesanan }}
                </div>
                <div class="mt-2 text-xs font-medium text-[#6F7D75]">
                    {{ $pesananMenunggu }} menunggu diproses
                </div>
            </div>
        </div>

        <!-- 3. Pengeluaran -->
        <div class="bg-white rounded-2xl p-5 border border-[#EBE8E0] shadow-xs flex flex-col justify-between transition hover:shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#6F7D75] font-medium">Pengeluaran</span>
                <div class="w-8 h-8 rounded-lg bg-[#EBF2EE] text-[#184233] flex items-center justify-center shrink-0">
                    <!-- Trending Up Icon -->
                    <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#1E2922] tracking-tight leading-none">
                    Rp {{ number_format($pengeluaranHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs font-medium text-[#6F7D75]">
                    {{ $jumlahTransaksiPengeluaran }} transaksi
                </div>
            </div>
        </div>

        <!-- 4. Stok menipis -->
        <div class="bg-white rounded-2xl p-5 border border-[#EBE8E0] shadow-xs flex flex-col justify-between transition hover:shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#6F7D75] font-medium">Stok menipis</span>
                <div class="w-8 h-8 rounded-lg bg-[#FEF7DC] text-[#BD852A] flex items-center justify-center shrink-0">
                    <!-- Alert Triangle Icon -->
                    <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-[26px] font-bold text-[#1E2922] tracking-tight leading-none">
                    {{ $stokMenipisCount }} bahan
                </div>
                <div class="mt-2 text-xs font-semibold text-[#BD852A]">
                    Perlu belanja
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Section: Chart Pendapatan 7 Hari & Menu Terlaris -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Pendapatan 7 hari terakhir (62% desktop / col-span-7) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 border border-[#EBE8E0] shadow-xs flex flex-col justify-between">
            <h2 class="text-base sm:text-lg font-bold text-[#1E2922]">Pendapatan 7 hari terakhir</h2>

            <div class="mt-6 pt-2 pb-1 overflow-x-auto custom-scrollbar">
                <div class="flex items-end justify-between gap-2 sm:gap-4 min-w-[320px] h-48 sm:h-52 px-1">
                    @foreach($chart7Hari as $bar)
                        <div class="flex-1 flex flex-col items-center justify-end h-full group">
                            <!-- Value Label Above Bar -->
                            <span class="text-[11px] sm:text-xs font-medium text-[#6F7D75] mb-2 tracking-tight transition-transform group-hover:-translate-y-0.5">
                                {{ $bar['label'] }}
                            </span>

                            <!-- Bar Graphic -->
                            <div class="w-full flex justify-center items-end h-32 sm:h-36">
                                <div 
                                    style="height: {{ $bar['tinggi'] }}%;" 
                                    class="w-full max-w-[42px] sm:max-w-[48px] {{ $bar['is_today'] ? 'bg-[#184233]' : 'bg-[#A3C2B4]' }} rounded-t-md sm:rounded-t-lg transition-all duration-300 hover:opacity-90"
                                    title="{{ $bar['hari'] }}: {{ $bar['label'] }}"
                                ></div>
                            </div>

                            <!-- Day Label Below Bar -->
                            <span class="text-xs sm:text-sm font-medium text-[#6F7D75] mt-3">
                                {{ $bar['hari'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Menu terlaris (38% desktop / col-span-5) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 border border-[#EBE8E0] shadow-xs flex flex-col justify-between">
            <h2 class="text-base sm:text-lg font-bold text-[#1E2922] mb-4">Menu terlaris</h2>

            <div class="space-y-4 sm:space-y-4.5 my-auto">
                @foreach($menuTerlaris as $menu)
                    <div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="font-semibold text-[#1E2922] truncate pr-2">{{ $menu['nama'] }}</span>
                            <span class="font-medium text-[#6F7D75] shrink-0">{{ $menu['terjual'] }} terjual</span>
                        </div>
                        <div class="w-full bg-[#EBF2EE] h-2 rounded-full overflow-hidden mt-1.5">
                            <div 
                                class="bg-[#184233] h-full rounded-full transition-all duration-500" 
                                style="width: {{ $menu['persen'] }}%;"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Section: Stok Menipis & Pesanan Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Stok menipis list -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-[#EBE8E0] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-bold text-[#1E2922]">Stok menipis</h2>
                <a href="{{ route('bahan.index') }}" class="text-xs text-[#184233] font-semibold hover:underline">
                    Kelola Stok &rarr;
                </a>
            </div>

            <div class="space-y-3.5 my-auto">
                @forelse($bahanMenipis as $item)
                    @php
                        $unitLabel = ($item->satuan === 'gram') ? 'g' : $item->satuan;
                    @endphp
                    <div class="flex items-center justify-between py-1 text-xs sm:text-sm">
                        <div class="flex items-center gap-2.5 min-w-0 pr-3">
                            <!-- Warning Triangle Icon -->
                            <svg class="w-4 h-4 text-[#BD852A] shrink-0 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <span class="font-semibold text-[#1E2922] truncate">{{ $item->nama }}</span>
                        </div>
                        <div class="text-[#6F7D75] font-medium shrink-0">
                            {{ number_format($item->stok, 0, ',', '.') }} {{ $unitLabel }} / min {{ number_format($item->stok_minimum, 0, ',', '.') }} {{ $unitLabel }}
                        </div>
                    </div>
                @empty
                    <p class="text-xs sm:text-sm text-[#6F7D75] italic py-2">Semua stok bahan dalam kondisi aman.</p>
                @endforelse
            </div>
        </div>

        <!-- Pesanan terbaru list -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-[#EBE8E0] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-bold text-[#1E2922]">Pesanan terbaru</h2>
            </div>

            <div class="space-y-3.5 my-auto">
                @forelse($pesananTerbaru as $order)
                    @php
                        $sumberText = $order->meja ? $order->meja->nomor_meja : 'Kasir';
                        $isMenunggu = strtolower($order->status) === 'menunggu';
                        $statusBadgeClass = $isMenunggu 
                            ? 'bg-[#FEF7DC] text-[#BD852A]' 
                            : 'bg-[#EBF2EE] text-[#184233]';
                        $statusLabel = ucfirst($order->status);
                    @endphp
                    <div class="flex items-center justify-between py-1 text-xs sm:text-sm">
                        <div class="font-semibold text-[#1E2922] truncate pr-2">
                            {{ $order->kode_pesanan }} | {{ $sumberText }}
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-semibold {{ $statusBadgeClass }}">
                                {{ $statusLabel }}
                            </span>
                            <span class="font-bold text-[#1E2922]">
                                Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs sm:text-sm text-[#6F7D75] italic py-2">Belum ada pesanan terbaru hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
