@extends('layouts.admin')

@section('title', 'Stok Bahan')

@section('content')
<div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

    <!-- Flash Notification -->
    @if(session('success'))
        <div id="flash-success" class="flex items-center justify-between bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs transition">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="document.getElementById('flash-success').remove()" class="text-emerald-600 hover:text-emerald-900 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-xs">
            <p class="font-semibold text-xs sm:text-sm">Terjadi kesalahan input:</p>
            <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header & Action Buttons (Responsif: Stack vertikal di HP, horizontal di Desktop) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-[#1B2721] tracking-tight">Stok Bahan</h1>
            <p class="text-xs sm:text-sm text-[#738078] mt-1">Pantau stok dan peringatan stok menipis</p>
        </div>
        <div class="flex items-center gap-2.5 sm:gap-3 w-full sm:w-auto">
            <!-- Tombol Catat Mutasi -->
            <button
                type="button"
                onclick="openMutasiModal()"
                class="flex-1 sm:flex-initial justify-center bg-white border border-[#E3E8E4] text-[#2C3831] px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-xs hover:bg-[#F5F8F6] active:bg-[#ECEEEB] transition flex items-center gap-2 cursor-pointer whitespace-nowrap"
            >
                Catat Mutasi
            </button>

            <!-- Tombol Tambah Bahan -->
            <button
                type="button"
                onclick="openTambahModal()"
                class="flex-1 sm:flex-initial justify-center bg-[#184233] text-white px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-xs hover:bg-[#133629] active:bg-[#0E271D] transition flex items-center gap-2 cursor-pointer whitespace-nowrap"
            >
                <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Tambah Bahan</span>
            </button>
        </div>
    </div>

    <!-- Alert Stok Menipis (Warning Banner) -->
    @if($menipisItems->count() > 0)
        <div class="bg-[#FEF7DC] border border-[#FBECC2] rounded-2xl px-4 sm:px-5 py-3 sm:py-3.5 flex items-start sm:items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-[#B6761A] shrink-0 fill-none stroke-current stroke-2 mt-0.5 sm:mt-0" viewBox="0 0 24 24">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <p class="text-xs sm:text-sm font-medium text-[#8F5B13] leading-relaxed">
                {{ $menipisItems->count() }} bahan berada di bawah batas minimum: {{ $menipisItems->pluck('nama')->implode(', ') }}.
            </p>
        </div>
    @endif

    <!-- Search & Filter Controls (Responsif: Full width di HP, Inline di Desktop) -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Search Input -->
        <form method="GET" action="{{ route('bahan.index') }}" class="relative flex-1 max-w-2xl">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-[#9AA59F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="2"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"></line>
                </svg>
            </div>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari bahan..."
                class="w-full bg-white border border-[#E3E8E4] rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-[#1B2721] placeholder-[#9AA59F] focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] transition shadow-xs"
                onchange="this.form.submit()"
            >
        </form>

        <!-- Status Filter Dropdown -->
        <form method="GET" action="{{ route('bahan.index') }}" class="shrink-0 w-full sm:w-auto">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <div class="relative w-full sm:w-auto">
                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto appearance-none bg-white border border-[#E3E8E4] rounded-xl pl-4 pr-10 py-2.5 text-xs sm:text-sm font-medium text-[#2C3831] focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233] transition shadow-xs cursor-pointer"
                >
                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua status</option>
                    <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Aman</option>
                    <option value="menipis" {{ request('status') == 'menipis' ? 'selected' : '' }}>Menipis</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#738078]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card (Responsif: Horizontal scroll halus dengan min-width terukur) -->
    <div class="bg-white rounded-2xl border border-[#E5EAE6] shadow-xs overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[680px] text-left border-collapse">
                <thead>
                    <tr class="bg-[#EBF2EE] border-b border-[#E1E8E3] text-[#2F4439] text-xs font-semibold">
                        <th class="py-3.5 px-4 sm:px-6">Bahan</th>
                        <th class="py-3.5 px-4 sm:px-6">Satuan</th>
                        <th class="py-3.5 px-4 sm:px-6">Stok</th>
                        <th class="py-3.5 px-4 sm:px-6">Batas min.</th>
                        <th class="py-3.5 px-4 sm:px-6">Level</th>
                        <th class="py-3.5 px-4 sm:px-6">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0F4F1] text-xs sm:text-sm">
                    @forelse($bahanList as $bahan)
                        @php
                            $isMenipis = $bahan->stok < $bahan->stok_minimum;
                            $percentage = $bahan->stok_minimum > 0
                                ? min(100, max(5, round(($bahan->stok / $bahan->stok_minimum) * 100)))
                                : 100;
                            if (!$isMenipis) {
                                $percentage = 100;
                            }
                        @endphp
                        <tr class="hover:bg-[#FAFBF9] transition-colors">
                            <!-- Nama Bahan -->
                            <td class="py-4 px-4 sm:px-6 font-bold text-[#1B2721] whitespace-nowrap">
                                {{ $bahan->nama }}
                            </td>

                            <!-- Satuan -->
                            <td class="py-4 px-4 sm:px-6 text-[#526057] whitespace-nowrap">
                                {{ $bahan->satuan }}
                            </td>

                            <!-- Stok -->
                            <td class="py-4 px-4 sm:px-6 font-bold text-[#1B2721] whitespace-nowrap">
                                {{ number_format($bahan->stok, 0, ',', '.') }}
                            </td>

                            <!-- Batas min. -->
                            <td class="py-4 px-4 sm:px-6 text-[#526057] whitespace-nowrap">
                                {{ number_format($bahan->stok_minimum, 0, ',', '.') }}
                            </td>

                            <!-- Level Progress Bar -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                <div class="w-24 sm:w-32 md:w-36 h-2 rounded-full bg-[#E5ECE7] overflow-hidden">
                                    <div
                                        class="h-full rounded-full transition-all duration-300 {{ $isMenipis ? 'bg-[#BD852A]' : 'bg-[#184835]' }}"
                                        style="width: {{ $percentage }}%"
                                    ></div>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                @if($isMenipis)
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold bg-[#FCEFD8] text-[#9A6B1F]">
                                        Menipis
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold bg-[#DEF0E5] text-[#1F6B43]">
                                        Aman
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2 sm:gap-3">
                                    <!-- Edit Button -->
                                    <button
                                        type="button"
                                        onclick="openEditModal({{ json_encode($bahan) }})"
                                        class="text-[#4A5568] hover:text-[#184233] p-1.5 rounded-lg hover:bg-emerald-50 transition cursor-pointer"
                                        title="Edit Bahan"
                                    >
                                        <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <button
                                        type="button"
                                        onclick="openDeleteModal('{{ $bahan->id }}', '{{ addslashes($bahan->nama) }}')"
                                        class="text-[#DC2626] hover:text-[#991B1B] p-1.5 rounded-lg hover:bg-red-50 transition cursor-pointer"
                                        title="Hapus Bahan"
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
                            <td colspan="7" class="py-10 text-center text-[#738078] text-xs sm:text-sm">
                                Tidak ada bahan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination & Summary (Responsif: Menyesuaikan di layar kecil) -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-[#738078]">
        <div class="text-center sm:text-left">
            Menampilkan {{ $bahanList->firstItem() ?? 0 }}-{{ $bahanList->lastItem() ?? 0 }} dari {{ $bahanList->total() }} bahan
        </div>

        @if($bahanList->hasPages())
            <div class="flex items-center gap-1.5">
                <!-- Previous Button -->
                @if ($bahanList->onFirstPage())
                    <span class="w-8 h-8 rounded-lg border border-[#E3E8E4] bg-white text-[#BAC4BD] flex items-center justify-center cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $bahanList->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E3E8E4] bg-white text-[#2C3831] hover:bg-[#F0F4F1] flex items-center justify-center transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif

                <!-- Page Links -->
                @foreach ($bahanList->getUrlRange(1, $bahanList->lastPage()) as $page => $url)
                    @if ($page == $bahanList->currentPage())
                        <span class="w-8 h-8 rounded-lg bg-[#184233] text-white font-bold text-xs flex items-center justify-center shadow-xs">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-[#E3E8E4] bg-white text-[#526057] hover:bg-[#F0F4F1] font-medium text-xs flex items-center justify-center transition shadow-xs">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                <!-- Next Button -->
                @if ($bahanList->hasMorePages())
                    <a href="{{ $bahanList->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E3E8E4] bg-white text-[#2C3831] hover:bg-[#F0F4F1] flex items-center justify-center transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="w-8 h-8 rounded-lg border border-[#E3E8E4] bg-white text-[#BAC4BD] flex items-center justify-center cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Bahan (Responsif: Header & Footer Permanen) -->
<div id="modalTambah" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 flex flex-col max-h-[90vh] overflow-hidden">
        <!-- Header (Permanen / Tidak bisa di-scroll) -->
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 bg-white shrink-0">
            <h3 class="font-bold text-base sm:text-lg text-[#1B2721]">Tambah Bahan Baru</h3>
            <button onclick="closeModal('modalTambah')" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('bahan.store') }}" class="flex flex-col flex-1 overflow-hidden min-h-0">
            @csrf

            <!-- Form Body (Hanya area ini yang dapat di-scroll) -->
            <div class="p-5 sm:p-6 space-y-3.5 sm:space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                <div>
                    <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Nama Bahan</label>
                    <input type="text" name="nama" required placeholder="Contoh: Kopi Arabica" class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Satuan</label>
                        <input type="text" name="satuan" required placeholder="gram, ml, pcs" class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Stok Awal</label>
                        <input type="number" step="any" min="0" name="stok" required placeholder="0" class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Batas Minimum (Peringatan)</label>
                    <input type="number" step="any" min="0" name="stok_minimum" required placeholder="Contoh: 1000" class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                    <p class="text-[11px] text-gray-400 mt-1">Sistem akan memberi peringatan jika stok berada di bawah angka ini.</p>
                </div>
            </div>

            <!-- Footer (Permanen di bagian bawah) -->
            <div class="flex items-center justify-end gap-2.5 sm:gap-3 px-5 sm:px-6 py-4 border-t border-gray-100 bg-[#FAF8F4] shrink-0">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs sm:text-sm font-semibold text-white bg-[#184233] hover:bg-[#133629] rounded-xl transition shadow-xs active:scale-95">Simpan Bahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Bahan (Responsif: Header & Footer Permanen) -->
<div id="modalEdit" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 flex flex-col max-h-[90vh] overflow-hidden">
        <!-- Header (Permanen / Tidak bisa di-scroll) -->
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 bg-white shrink-0">
            <h3 class="font-bold text-base sm:text-lg text-[#1B2721]">Edit Bahan</h3>
            <button onclick="closeModal('modalEdit')" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formEdit" method="POST" action="" class="flex flex-col flex-1 overflow-hidden min-h-0">
            @csrf
            @method('PUT')

            <!-- Form Body (Hanya area ini yang dapat di-scroll) -->
            <div class="p-5 sm:p-6 space-y-3.5 sm:space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                <div>
                    <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Nama Bahan</label>
                    <input type="text" id="editNama" name="nama" required class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Satuan</label>
                        <input type="text" id="editSatuan" name="satuan" required class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Stok Saat Ini</label>
                        <input type="number" step="any" min="0" id="editStok" name="stok" required class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Batas Minimum</label>
                    <input type="number" step="any" min="0" id="editStokMinimum" name="stok_minimum" required class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>
            </div>

            <!-- Footer (Permanen di bagian bawah) -->
            <div class="flex items-center justify-end gap-2.5 sm:gap-3 px-5 sm:px-6 py-4 border-t border-gray-100 bg-[#FAF8F4] shrink-0">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs sm:text-sm font-semibold text-white bg-[#184233] hover:bg-[#133629] rounded-xl transition shadow-xs active:scale-95">Perbarui Bahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Catat Mutasi (Responsif: Header & Footer Permanen) -->
<div id="modalMutasi" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 flex flex-col max-h-[90vh] overflow-hidden">
        <!-- Header (Permanen / Tidak bisa di-scroll) -->
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 bg-white shrink-0">
            <div>
                <h3 class="font-bold text-base sm:text-lg text-[#1B2721]">Catat Mutasi Stok</h3>
                <p class="text-[11px] sm:text-xs text-[#738078] mt-0.5">Catat stok masuk, stok keluar, atau penyesuaian</p>
            </div>
            <button onclick="closeModal('modalMutasi')" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('bahan.mutasi') }}" class="flex flex-col flex-1 overflow-hidden min-h-0">
            @csrf

            <!-- Form Body (Hanya area ini yang dapat di-scroll) -->
            <div class="p-5 sm:p-6 space-y-3.5 sm:space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                <div>
                    <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Pilih Bahan</label>
                    <select name="bahan_id" required class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                        <option value="">-- Pilih bahan --</option>
                        @foreach($allBahan as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->nama }} (Stok saat ini: {{ number_format($b->stok, 0, ',', '.') }} {{ $b->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Jenis Mutasi</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center justify-center gap-1.5 p-2 sm:p-2.5 rounded-xl border border-[#E3E8E4] cursor-pointer hover:bg-emerald-50/50 has-checked:border-[#184233] has-checked:bg-[#184233]/10">
                            <input type="radio" name="jenis" value="masuk" checked class="text-[#184233] focus:ring-[#184233]">
                            <span class="text-[11px] sm:text-xs font-semibold text-[#2F4439]">Masuk (+)</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2 sm:p-2.5 rounded-xl border border-[#E3E8E4] cursor-pointer hover:bg-red-50/50 has-checked:border-red-600 has-checked:bg-red-50">
                            <input type="radio" name="jenis" value="keluar" class="text-red-600 focus:ring-red-600">
                            <span class="text-[11px] sm:text-xs font-semibold text-[#2F4439]">Keluar (-)</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2 sm:p-2.5 rounded-xl border border-[#E3E8E4] cursor-pointer hover:bg-amber-50/50 has-checked:border-amber-600 has-checked:bg-amber-50">
                            <input type="radio" name="jenis" value="penyesuaian" class="text-amber-600 focus:ring-amber-600">
                            <span class="text-[11px] sm:text-xs font-semibold text-[#2F4439]">Sesuaikan</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Jumlah</label>
                    <input type="number" step="any" min="0.01" name="jumlah" required placeholder="Masukkan jumlah" class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#2F4439] uppercase tracking-wider mb-1.5">Catatan (Opsional)</label>
                    <input type="text" name="catatan" placeholder="Contoh: Pembelian agen, terbuang, dll" class="w-full bg-[#FAFBF9] border border-[#E3E8E4] rounded-xl px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#184233]/20 focus:border-[#184233]">
                </div>
            </div>

            <!-- Footer (Permanen di bagian bawah) -->
            <div class="flex items-center justify-end gap-2.5 sm:gap-3 px-5 sm:px-6 py-4 border-t border-gray-100 bg-[#FAF8F4] shrink-0">
                <button type="button" onclick="closeModal('modalMutasi')" class="px-4 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs sm:text-sm font-semibold text-white bg-[#184233] hover:bg-[#133629] rounded-xl transition shadow-xs active:scale-95">Simpan Mutasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="modalHapus" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border border-gray-100 transition-all text-center">
        <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="font-bold text-base sm:text-lg text-[#1B2721]">Hapus Bahan?</h3>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            Apakah Anda yakin ingin menghapus <strong id="hapusNamaBahan" class="text-gray-800"></strong>? Tindakan ini tidak dapat dibatalkan.
        </p>

        <form id="formHapus" method="POST" action="" class="mt-5 sm:mt-6 flex items-center justify-center gap-2.5 sm:gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeModal('modalHapus')" class="flex-1 px-4 py-2.5 text-xs sm:text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Batal</button>
            <button type="submit" class="flex-1 px-4 py-2.5 text-xs sm:text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition shadow-xs">Ya, Hapus</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const bahanBaseUrl = "{{ url('bahan') }}";

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openTambahModal() {
        openModal('modalTambah');
    }

    function openMutasiModal() {
        openModal('modalMutasi');
    }

    function openEditModal(bahan) {
        document.getElementById('editNama').value = bahan.nama;
        document.getElementById('editSatuan').value = bahan.satuan;
        document.getElementById('editStok').value = bahan.stok;
        document.getElementById('editStokMinimum').value = bahan.stok_minimum;
        document.getElementById('formEdit').action = bahanBaseUrl + '/' + bahan.id;
        openModal('modalEdit');
    }

    function openDeleteModal(id, nama) {
        document.getElementById('hapusNamaBahan').innerText = nama;
        document.getElementById('formHapus').action = bahanBaseUrl + '/' + id;
        openModal('modalHapus');
    }

    // Tutup modal jika klik di luar box
    window.addEventListener('click', function(e) {
        ['modalTambah', 'modalEdit', 'modalMutasi', 'modalHapus'].forEach(function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal && e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
@endpush
@endsection
