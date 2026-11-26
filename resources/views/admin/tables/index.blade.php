<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>A7 Meja & QR Code - Panel Admin De Barrio</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FBF8EE] text-[#1B2B24] min-h-screen flex flex-col md:flex-row antialiased">

    <!-- Mobile Topbar for Admin -->
    <header class="md:hidden bg-[#1F4D3A] text-white p-4 flex items-center justify-between sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-[#F6EFD8] flex items-center justify-center text-[#1F4D3A]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3" />
                </svg>
            </div>
            <div>
                <span class="font-bold text-sm block">De Barrio</span>
                <span class="text-[10px] text-[#A9C4B5] block">Panel Admin</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('cart') }}" class="text-xs bg-[#2E6B52] px-2.5 py-1 rounded-lg text-white font-medium">Lihat Menu</a>
            <button type="button" onclick="document.getElementById('admin-sidebar').classList.toggle('hidden')" class="p-1.5 rounded-lg bg-[#2E6B52] text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Sidebar Admin (Sesuai Figma Node 7:763) -->
    <aside id="admin-sidebar" class="hidden md:flex flex-col bg-[#1F4D3A] w-full md:w-[240px] shrink-0 min-h-screen p-4 text-white z-30 transition-all">
        <!-- Logo / Brand -->
        <div class="flex items-center gap-3 pb-5 px-2 border-b border-[#2E6B52]">
            <div class="w-10 h-10 rounded-full bg-[#F6EFD8] flex items-center justify-center text-[#1F4D3A] shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3" />
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-base text-white tracking-tight leading-tight">De Barrio</h1>
                <p class="text-xs font-medium text-[#A9C4B5]">Panel Admin</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-1.5 my-4 flex-1">
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Menu & Kategori</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Stok Bahan</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Pengeluaran</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Laporan</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Pengguna</span>
            </a>
            
            <!-- Meja & QR (Active State) -->
            <a href="{{ route('admin.tables.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold bg-[#2E6B52] text-white shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span>Meja & QR</span>
            </a>
        </nav>

        <!-- Profile Section -->
        <div class="pt-4 border-t border-[#2E6B52]">
            <div class="bg-[#2E6B52] p-3 rounded-xl flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-[#F6EFD8] text-[#1F4D3A] flex items-center justify-center font-bold text-xs shrink-0">
                    MF
                </div>
                <div class="truncate flex-1 min-w-0">
                    <p class="font-bold text-xs text-white truncate">M. Fikri Firdaus</p>
                    <p class="text-[11px] text-[#BFD3C7] truncate">Admin / Owner</p>
                </div>
                <a href="{{ route('cart') }}" class="text-[#BFD3C7] hover:text-white transition" title="Ke Pelanggan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content (Sesuai Figma Node 7:826) -->
    <main class="flex-1 p-5 sm:p-7 md:p-8 overflow-y-auto">
        <!-- Breadcrumb / Link Back to Customer -->
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2 text-xs text-[#66776D]">
            <div class="flex items-center gap-1.5">
                <span>Panel Admin</span>
                <span>/</span>
                <span class="text-[#1F4D3A] font-semibold">A7 Meja & QR Code</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('cart') }}" class="inline-flex items-center gap-1 text-[#1F4D3A] font-semibold hover:underline bg-[#DDEEE3] px-2.5 py-1 rounded-lg">
                    📱 Buka Halaman Pelanggan (Meja 05) →
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 bg-[#DDEEE3] border border-[#1F4D3A]/20 text-[#1F4D3A] px-4 py-3 rounded-xl text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-xs font-bold">×</button>
            </div>
        @endif

        <!-- Header Halaman -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
            <div>
                <h2 class="text-2xl sm:text-[26px] font-bold text-[#1B2B24] tracking-tight">Meja & QR Code</h2>
                <p class="text-xs sm:text-[13px] text-[#66776D] mt-1">
                    Buat QR code untuk pemesanan mandiri di setiap meja
                </p>
            </div>
            <div>
                <button type="button" 
                        onclick="document.getElementById('modal-tambah-meja').classList.remove('hidden')" 
                        class="bg-[#1F4D3A] hover:bg-[#143529] text-white px-4 py-2.5 rounded-[10px] text-xs sm:text-[13px] font-semibold inline-flex items-center gap-2 shadow-xs transition cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Meja</span>
                </button>
            </div>
        </div>

        <!-- Grid Kartu Meja (Responsive: Mobile 1-col, Tablet 2-col, Desktop 4-col) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 mb-8">
            @forelse($tables as $table)
                <div class="bg-white border border-[#E6E0CC] rounded-[14px] p-3.5 shadow-2xs hover:shadow-md transition flex flex-col justify-between">
                    <!-- Top Section: QR Box -->
                    <div>
                        <div class="bg-[#FBF8EE] rounded-[12px] h-[130px] flex items-center justify-center p-3 mb-3 relative overflow-hidden group">
                            <!-- Crisp Vector QR Code SVG -->
                            <div class="w-20 h-20 text-[#1B2B24] flex items-center justify-center">
                                {!! \App\Services\QrCodeService::generateSvg($table->qr_url, 80, '#1B2B24') !!}
                            </div>

                            <!-- Overlay Quick Scan Link on hover -->
                            <a href="{{ route('cart', ['table' => $table->table_number]) }}" target="_blank" class="absolute inset-0 bg-[#1F4D3A]/80 text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition p-2 text-center text-xs font-semibold">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <span>Buka Meja</span>
                            </a>
                        </div>

                        <!-- Table Name & Status Badge -->
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h3 class="font-bold text-[15px] text-[#1B2B24] truncate">{{ $table->name }}</h3>
                            
                            <!-- Status Toggle Form -->
                            <form action="{{ route('admin.tables.toggle', $table->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        title="Klik untuk ubah status"
                                        class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold transition cursor-pointer
                                        {{ $table->status === 'aktif' ? 'bg-[#DDEEE3] text-[#1F4D3A] hover:bg-[#c9e3d2]' : 'bg-[#FBE7E3] text-[#B3402F] hover:bg-[#fad4cc]' }}">
                                    {{ ucfirst($table->status) }}
                                </button>
                            </form>
                        </div>

                        <!-- Token Text -->
                        <p class="text-[11px] text-[#66776D] font-normal mb-3">
                            Token: <span class="font-mono text-gray-700">{{ $table->token }}</span>
                        </p>
                    </div>

                    <!-- Action Buttons Row (Unduh & Cetak) -->
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.tables.download-qr', $table->id) }}" 
                           class="flex-1 bg-white border border-[#E6E0CC] hover:bg-gray-50 text-[#1B2B24] py-2 px-2 rounded-[10px] text-[13px] font-semibold flex items-center justify-center gap-1.5 transition text-center shadow-2xs">
                            <svg class="w-4 h-4 text-[#66776D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh</span>
                        </a>

                        <a href="{{ route('admin.tables.print', $table->id) }}" 
                           target="_blank"
                           class="flex-1 bg-white border border-[#E6E0CC] hover:bg-gray-50 text-[#1B2B24] py-2 px-2 rounded-[10px] text-[13px] font-semibold flex items-center justify-center gap-1.5 transition text-center shadow-2xs">
                            <svg class="w-4 h-4 text-[#66776D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-12 text-center text-gray-400">
                    Belum ada meja yang terdaftar. Silakan tambahkan meja baru.
                </div>
            @endforelse
        </div>

        <!-- Paginasi (Sesuai Figma Node 7:762) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#66776D] pt-4 border-t border-[#E6E0CC]">
            <p>
                Menampilkan {{ $tables->count() }} dari {{ $totalTables }} meja
            </p>
            <div class="flex items-center gap-1.5">
                @if($tables->onFirstPage())
                    <span class="w-8 h-8 rounded-lg bg-gray-100 border border-[#E6E0CC] flex items-center justify-center text-gray-400 cursor-not-allowed">&lt;</span>
                @else
                    <a href="{{ $tables->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-[#E6E0CC] flex items-center justify-center text-[#1B2B24] hover:bg-gray-50">&lt;</a>
                @endif

                @for($page = 1; $page <= $tables->lastPage(); $page++)
                    @if($page == $tables->currentPage())
                        <span class="w-8 h-8 rounded-lg bg-[#1F4D3A] text-white font-semibold flex items-center justify-center">{{ $page }}</span>
                    @else
                        <a href="{{ $tables->url($page) }}" class="w-8 h-8 rounded-lg bg-white border border-[#E6E0CC] text-[#1B2B24] font-semibold flex items-center justify-center hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endfor

                @if($tables->hasMorePages())
                    <a href="{{ $tables->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-[#E6E0CC] flex items-center justify-center text-[#1B2B24] hover:bg-gray-50">&gt;</a>
                @else
                    <span class="w-8 h-8 rounded-lg bg-gray-100 border border-[#E6E0CC] flex items-center justify-center text-gray-400 cursor-not-allowed">&gt;</span>
                @endif
            </div>
        </div>
    </main>

    <!-- Modal Tambah Meja -->
    <div id="modal-tambah-meja" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 animate-in fade-in duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="font-bold text-base text-[#1B2B24]">Tambah Meja Baru</h3>
                <button type="button" onclick="document.getElementById('modal-tambah-meja').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg font-bold">×</button>
            </div>

            <form action="{{ route('admin.tables.store') }}" method="POST">
                @csrf
                <div class="space-y-4 mb-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Meja *</label>
                        <input type="text" name="table_number" placeholder="Contoh: 09" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#1F4D3A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Meja (Opsional)</label>
                        <input type="text" name="name" placeholder="Contoh: Meja 09 (Outdoor)" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#1F4D3A] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status Awal</label>
                        <select name="status" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:border-[#1F4D3A] focus:outline-hidden">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-tambah-meja').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-[#1F4D3A] text-white hover:bg-[#143529]">Simpan Meja</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
