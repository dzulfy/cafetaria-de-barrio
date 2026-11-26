@extends('layouts.admin')

@section('title', 'Pengeluaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Flash Message Notifikasi Sukses -->
    @if(session('success'))
        <div id="alertSuccess" class="flex items-center justify-between p-4 rounded-xl bg-[#DEF0E5] border border-[#BDE0CD] text-[#184835] text-sm shadow-xs transition duration-300">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 shrink-0 stroke-current stroke-2 fill-none" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('alertSuccess').remove()" class="text-[#184835] hover:opacity-75 p-1">
                <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    @endif

    <!-- Header Section (Judul, Subtitle, & Tombol Catat Pengeluaran) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[28px] font-bold text-[#1E2922] tracking-tight leading-tight">Pengeluaran</h1>
            <p class="text-xs sm:text-sm text-[#6F7D75] font-medium mt-0.5">
                Catat dan pantau pengeluaran operasional
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto w-full sm:w-auto">
            <button 
                type="button" 
                onclick="openModalTambah()"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#184233] text-white text-xs sm:text-sm font-semibold shadow-xs hover:bg-[#224E3D] active:scale-95 transition"
            >
                <!-- Plus Icon -->
                <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Catat Pengeluaran</span>
            </button>
        </div>
    </div>

    <!-- 3 Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        <!-- 1. Total bulan ini (Red Text) -->
        <div class="bg-white rounded-2xl p-5 border border-[#EBE8E0] shadow-xs flex flex-col justify-between transition hover:shadow-sm">
            <span class="text-xs text-[#6F7D75] font-medium">Total bulan ini</span>
            <div class="text-2xl sm:text-[26px] font-bold text-[#B91C1C] tracking-tight mt-2 leading-none">
                Rp {{ number_format($totalBulanIni, 0, ',', '.') }}
            </div>
        </div>

        <!-- 2. Hari ini -->
        <div class="bg-white rounded-2xl p-5 border border-[#EBE8E0] shadow-xs flex flex-col justify-between transition hover:shadow-sm">
            <span class="text-xs text-[#6F7D75] font-medium">Hari ini</span>
            <div class="text-2xl sm:text-[26px] font-bold text-[#1E2922] tracking-tight mt-2 leading-none">
                Rp {{ number_format($totalHariIni, 0, ',', '.') }}
            </div>
        </div>

        <!-- 3. Jumlah catatan -->
        <div class="bg-white rounded-2xl p-5 border border-[#EBE8E0] shadow-xs flex flex-col justify-between transition hover:shadow-sm">
            <span class="text-xs text-[#6F7D75] font-medium">Jumlah catatan</span>
            <div class="text-2xl sm:text-[26px] font-bold text-[#1E2922] tracking-tight mt-2 leading-none">
                {{ $jumlahCatatan }}
            </div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- Search Input -->
        <form action="{{ route('pengeluaran.index') }}" method="GET" class="relative flex-1 max-w-md">
            @if($filterPencatat)
                <input type="hidden" name="pencatat" value="{{ $filterPencatat }}">
            @endif
            @if($filterKategori)
                <input type="hidden" name="kategori" value="{{ $filterKategori }}">
            @endif
            @if($filterStartDate && $filterEndDate)
                <input type="hidden" name="start_date" value="{{ $filterStartDate }}">
                <input type="hidden" name="end_date" value="{{ $filterEndDate }}">
            @endif

            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8DAAA0]">
                <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <input 
                type="text" 
                name="search" 
                value="{{ $filterSearch }}" 
                placeholder="Cari uraian..." 
                class="w-full pl-10 pr-4 py-2.5 bg-white border border-[#EBE8E0] rounded-xl text-xs sm:text-sm text-[#1E2922] placeholder-[#8DAAA0] focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] transition"
            >
        </form>

        <!-- Right Side Filters: Rentang Tanggal & Pencatat -->
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5">
            <!-- Filter Tanggal Dropdown Button -->
            <div class="relative inline-block text-left flex-1 sm:flex-initial">
                <button 
                    type="button" 
                    onclick="toggleDateFilter()"
                    class="w-full inline-flex items-center justify-between sm:justify-start gap-2 px-3.5 py-2.5 bg-white border border-[#EBE8E0] rounded-xl text-xs sm:text-sm font-medium text-[#1E2922] shadow-xs hover:bg-[#FAF8F4] transition"
                >
                    <svg class="w-4 h-4 text-[#184233] stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span class="truncate">
                        @if($filterStartDate && $filterEndDate)
                            {{ \Carbon\Carbon::parse($filterStartDate)->isoFormat('D MMM') }} - {{ \Carbon\Carbon::parse($filterEndDate)->isoFormat('D MMM Y') }}
                        @else
                            1 Nov - 18 Nov 2026
                        @endif
                    </span>
                    <svg class="w-3.5 h-3.5 text-[#6F7D75] stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <!-- Date Range Dropdown Popover -->
                <div id="dateFilterDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-[#EBE8E0] p-4 z-20">
                    <form action="{{ route('pengeluaran.index') }}" method="GET" class="space-y-3">
                        @if($filterSearch)
                            <input type="hidden" name="search" value="{{ $filterSearch }}">
                        @endif
                        @if($filterPencatat)
                            <input type="hidden" name="pencatat" value="{{ $filterPencatat }}">
                        @endif
                        @if($filterKategori)
                            <input type="hidden" name="kategori" value="{{ $filterKategori }}">
                        @endif

                        <div class="text-xs font-semibold text-[#1E2922]">Pilih Rentang Tanggal</div>
                        <div>
                            <label class="block text-[11px] text-[#6F7D75] mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $filterStartDate ?: now()->startOfMonth()->toDateString() }}" class="w-full text-xs p-2 border border-[#EBE8E0] rounded-lg">
                        </div>
                        <div>
                            <label class="block text-[11px] text-[#6F7D75] mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $filterEndDate ?: now()->toDateString() }}" class="w-full text-xs p-2 border border-[#EBE8E0] rounded-lg">
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <button type="submit" class="flex-1 py-1.5 bg-[#184233] text-white text-xs font-medium rounded-lg hover:bg-[#224E3D]">Terapkan</button>
                            <a href="{{ route('pengeluaran.index') }}" class="py-1.5 px-3 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 text-center">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filter Pencatat Dropdown -->
            <form id="filterPencatatForm" action="{{ route('pengeluaran.index') }}" method="GET" class="flex-1 sm:flex-initial">
                @if($filterSearch)
                    <input type="hidden" name="search" value="{{ $filterSearch }}">
                @endif
                @if($filterStartDate && $filterEndDate)
                    <input type="hidden" name="start_date" value="{{ $filterStartDate }}">
                    <input type="hidden" name="end_date" value="{{ $filterEndDate }}">
                @endif
                @if($filterKategori)
                    <input type="hidden" name="kategori" value="{{ $filterKategori }}">
                @endif

                <select 
                    name="pencatat" 
                    onchange="this.form.submit()"
                    class="w-full px-3.5 py-2.5 bg-white border border-[#EBE8E0] rounded-xl text-xs sm:text-sm font-medium text-[#1E2922] shadow-xs focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] transition cursor-pointer"
                >
                    <option value="" {{ empty($filterPencatat) ? 'selected' : '' }}>Semua pencatat</option>
                    <option value="Owner" {{ $filterPencatat === 'Owner' ? 'selected' : '' }}>Owner</option>
                    <option value="Ayu Lestari" {{ $filterPencatat === 'Ayu Lestari' ? 'selected' : '' }}>Ayu Lestari</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Table Container (Responsive Horizontal Scrollable) -->
    <div class="bg-white rounded-2xl border border-[#EBE8E0] shadow-xs overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead>
                    <tr class="bg-[#EBF2EE] text-[#1E2922] text-xs font-semibold border-b border-[#EBE8E0]">
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Uraian</th>
                        <th class="px-5 py-3.5">Pencatat</th>
                        <th class="px-5 py-3.5">Nominal</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1EFEA] text-xs sm:text-sm">
                    @forelse($pengeluaranList as $item)
                        @php
                            $pencatatNama = $item->pengguna?->peran === 'admin' ? 'Owner' : ($item->pengguna?->nama ?? 'Owner');
                            $tanggalFormatted = \Carbon\Carbon::parse($item->tanggal_pengeluaran)->locale('id')->isoFormat('D MMM Y');
                        @endphp
                        <tr class="hover:bg-[#FAF8F4]/70 transition">
                            <!-- Tanggal -->
                            <td class="px-5 py-4 font-semibold text-[#1E2922] whitespace-nowrap">
                                {{ $tanggalFormatted }}
                            </td>

                            <!-- Uraian -->
                            <td class="px-5 py-4 font-medium text-[#1E2922]">
                                {{ $item->deskripsi }}
                                @if($item->catatan)
                                    <span class="block text-[11px] text-[#6F7D75] font-normal">{{ $item->catatan }}</span>
                                @endif
                            </td>

                            <!-- Pencatat -->
                            <td class="px-5 py-4 text-[#4A5568] whitespace-nowrap">
                                {{ $pencatatNama }}
                            </td>

                            <!-- Nominal (Red Text matching reference) -->
                            <td class="px-5 py-4 font-bold text-[#B91C1C] whitespace-nowrap">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>

                            <!-- Kategori -->
                            <td class="px-5 py-4 text-[#4A5568] whitespace-nowrap">
                                {{ $item->kategori ?? 'Operasional' }}
                            </td>

                            <!-- Aksi (Edit & Hapus) -->
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <!-- Edit Button (Pencil) -->
                                    <button 
                                        type="button" 
                                        onclick="openModalEdit({{ json_encode($item) }})"
                                        class="p-1.5 rounded-lg text-[#6F7D75] hover:text-[#184233] hover:bg-[#EBF2EE] transition"
                                        title="Edit Pengeluaran"
                                    >
                                        <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </button>

                                    <!-- Delete Button (Trash) -->
                                    <button 
                                        type="button" 
                                        onclick="openModalHapus({{ $item->id }}, '{{ addslashes($item->deskripsi) }}')"
                                        class="p-1.5 rounded-lg text-[#D05D5D] hover:text-[#B91C1C] hover:bg-[#FDF2F2] transition"
                                        title="Hapus Pengeluaran"
                                    >
                                        <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-[#6F7D75] text-xs sm:text-sm italic">
                                Belum ada catatan pengeluaran yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom Pagination Section -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
        <div class="text-xs text-[#6F7D75] font-medium order-2 sm:order-1">
            Menampilkan {{ $pengeluaranList->firstItem() ?? 0 }}-{{ $pengeluaranList->lastItem() ?? 0 }} dari {{ $pengeluaranList->total() }} catatan
        </div>

        @if ($pengeluaranList->hasPages())
            <div class="inline-flex items-center gap-1.5 order-1 sm:order-2">
                {{-- Previous Page Link --}}
                @if ($pengeluaranList->onFirstPage())
                    <span class="w-8 h-8 rounded-lg border border-[#EBE8E0] bg-white text-[#CBD7D0] flex items-center justify-center text-xs font-semibold cursor-not-allowed">
                        &lt;
                    </span>
                @else
                    <a href="{{ $pengeluaranList->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#EBE8E0] bg-white text-[#1E2922] hover:bg-[#EBF2EE] flex items-center justify-center text-xs font-semibold transition">
                        &lt;
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($pengeluaranList->getUrlRange(1, $pengeluaranList->lastPage()) as $page => $url)
                    @if ($page == $pengeluaranList->currentPage())
                        <span class="w-8 h-8 rounded-lg bg-[#184233] text-white flex items-center justify-center text-xs font-semibold shadow-xs">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-[#EBE8E0] bg-white text-[#1E2922] hover:bg-[#EBF2EE] flex items-center justify-center text-xs font-semibold transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($pengeluaranList->hasMorePages())
                    <a href="{{ $pengeluaranList->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#EBE8E0] bg-white text-[#1E2922] hover:bg-[#EBF2EE] flex items-center justify-center text-xs font-semibold transition">
                        &gt;
                    </a>
                @else
                    <span class="w-8 h-8 rounded-lg border border-[#EBE8E0] bg-white text-[#CBD7D0] flex items-center justify-center text-xs font-semibold cursor-not-allowed">
                        &gt;
                    </span>
                @endif
            </div>
        @else
            <div class="inline-flex items-center gap-1.5 order-1 sm:order-2">
                <span class="w-8 h-8 rounded-lg border border-[#EBE8E0] bg-white text-[#CBD7D0] flex items-center justify-center text-xs font-semibold cursor-not-allowed">&lt;</span>
                <span class="w-8 h-8 rounded-lg bg-[#184233] text-white flex items-center justify-center text-xs font-semibold shadow-xs">1</span>
                <span class="w-8 h-8 rounded-lg border border-[#EBE8E0] bg-white text-[#CBD7D0] flex items-center justify-center text-xs font-semibold cursor-not-allowed">&gt;</span>
            </div>
        @endif
    </div>

</div>

<!-- Modal 1: Tambah Pengeluaran Baru -->
<div id="modalTambah" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header Modal (Permanen / Tidak bisa di-scroll) -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#EBE8E0] bg-white shrink-0">
            <h2 class="text-base sm:text-lg font-bold text-[#1E2922]">Catat Pengeluaran Baru</h2>
            <button type="button" onclick="closeModal('modalTambah')" class="p-1 rounded-lg text-[#6F7D75] hover:text-[#1E2922] hover:bg-gray-100 transition">
                <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <form action="{{ route('pengeluaran.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
            @csrf

            <!-- Form Content (Hanya area ini yang dapat di-scroll) -->
            <div class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pengeluaran" value="{{ now()->toDateString() }}" required class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>

                <!-- Uraian -->
                <div>
                    <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Uraian / Deskripsi <span class="text-red-500">*</span></label>
                    <input type="text" name="deskripsi" placeholder="cth: Belanja bahan baku mingguan" required class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nominal -->
                    <div>
                        <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" placeholder="cth: 245000" min="0" required class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori" required class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                            <option value="Bahan baku">Bahan baku</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <!-- Pencatat -->
                <div>
                    <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Pencatat</label>
                    <select name="pengguna_id" class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                        @foreach($penggunaList as $user)
                            <option value="{{ $user->id }}" {{ $user->peran === 'admin' ? 'selected' : '' }}>
                                {{ $user->peran === 'admin' ? 'Owner (' . $user->nama . ')' : $user->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan tambahan mengenai pengeluaran..." class="w-full px-3.5 py-2 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]"></textarea>
                </div>
            </div>

            <!-- Footer Modal (Permanen / Tidak bisa di-scroll di bagian bawah) -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[#EBE8E0] bg-[#FAF8F4] shrink-0">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2.5 rounded-xl border border-[#EBE8E0] bg-white text-[#6F7D75] text-xs sm:text-sm font-semibold hover:bg-gray-50 active:scale-95 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#184233] text-white text-xs sm:text-sm font-semibold hover:bg-[#224E3D] shadow-xs active:scale-95 transition">
                    Simpan Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Pengeluaran -->
<div id="modalEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header Modal (Permanen / Tidak bisa di-scroll) -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#EBE8E0] bg-white shrink-0">
            <h2 class="text-base sm:text-lg font-bold text-[#1E2922]">Edit Data Pengeluaran</h2>
            <button type="button" onclick="closeModal('modalEdit')" class="p-1 rounded-lg text-[#6F7D75] hover:text-[#1E2922] hover:bg-gray-100 transition">
                <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <form id="formEditPengeluaran" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
            @csrf
            @method('PUT')

            <!-- Form Content (Hanya area ini yang dapat di-scroll) -->
            <div class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
                    <input type="date" id="editTanggal" name="tanggal_pengeluaran" required class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>

                <!-- Uraian -->
                <div>
                    <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Uraian / Deskripsi <span class="text-red-500">*</span></label>
                    <input type="text" id="editDeskripsi" name="deskripsi" required class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nominal -->
                    <div>
                        <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="editJumlah" name="jumlah" min="0" required class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Kategori <span class="text-red-500">*</span></label>
                        <select id="editKategori" name="kategori" required class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                            <option value="Bahan baku">Bahan baku</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <!-- Pencatat -->
                <div>
                    <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Pencatat</label>
                    <select id="editPenggunaId" name="pengguna_id" class="w-full px-3.5 py-2.5 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                        @foreach($penggunaList as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->peran === 'admin' ? 'Owner (' . $user->nama . ')' : $user->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-semibold text-[#1E2922] mb-1.5">Catatan (Opsional)</label>
                    <textarea id="editCatatan" name="catatan" rows="2" class="w-full px-3.5 py-2 border border-[#EBE8E0] rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]"></textarea>
                </div>
            </div>

            <!-- Footer Modal (Permanen / Tidak bisa di-scroll di bagian bawah) -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[#EBE8E0] bg-[#FAF8F4] shrink-0">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2.5 rounded-xl border border-[#EBE8E0] bg-white text-[#6F7D75] text-xs sm:text-sm font-semibold hover:bg-gray-50 active:scale-95 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#184233] text-white text-xs sm:text-sm font-semibold hover:bg-[#224E3D] shadow-xs active:scale-95 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Konfirmasi Hapus -->
<div id="modalHapus" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl">
        <div class="text-center">
            <div class="w-12 h-12 rounded-full bg-[#FDF2F2] text-[#B91C1C] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 stroke-current stroke-2 fill-none" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </div>
            <h3 class="text-base font-bold text-[#1E2922]">Hapus Catatan Pengeluaran?</h3>
            <p class="text-xs text-[#6F7D75] mt-1.5" id="hapusText">Apakah Anda yakin ingin menghapus data pengeluaran ini?</p>
        </div>

        <form id="formHapusPengeluaran" method="POST" class="mt-6 flex items-center gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeModal('modalHapus')" class="flex-1 py-2.5 rounded-xl border border-[#EBE8E0] text-[#6F7D75] text-xs font-semibold hover:bg-gray-50">
                Batal
            </button>
            <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#B91C1C] text-white text-xs font-semibold hover:bg-[#991B1B] shadow-xs">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleDateFilter() {
        const dropdown = document.getElementById('dateFilterDropdown');
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('dateFilterDropdown');
        if (!e.target.closest('#dateFilterDropdown') && !e.target.closest('button[onclick="toggleDateFilter()"]')) {
            dropdown.classList.add('hidden');
        }
    });

    function openModalTambah() {
        document.getElementById('modalTambah').classList.remove('hidden');
    }

    function openModalEdit(item) {
        document.getElementById('formEditPengeluaran').action = "{{ url('pengeluaran') }}/" + item.id;
        document.getElementById('editTanggal').value = item.tanggal_pengeluaran.split('T')[0];
        document.getElementById('editDeskripsi').value = item.deskripsi;
        document.getElementById('editJumlah').value = Math.round(item.jumlah);
        document.getElementById('editKategori').value = item.kategori || 'Operasional';
        document.getElementById('editPenggunaId').value = item.pengguna_id || '';
        document.getElementById('editCatatan').value = item.catatan || '';

        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function openModalHapus(id, deskripsi) {
        document.getElementById('formHapusPengeluaran').action = "{{ url('pengeluaran') }}/" + id;
        document.getElementById('hapusText').innerText = 'Apakah Anda yakin ingin menghapus pengeluaran "' + deskripsi + '"? Tindakan ini tidak dapat dibatalkan.';
        document.getElementById('modalHapus').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endpush
@endsection
