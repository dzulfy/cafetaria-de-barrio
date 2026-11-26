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
        .bg-canvas { background-color: #FAF7F0; }
        .qr-placeholder-svg { stroke: #2D3748; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; fill: none; }
    </style>
</head>
<body class="bg-canvas text-[#1B2B24] min-h-screen flex flex-col md:flex-row antialiased">

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
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Menu & Kategori</span>
            </a>
            <a href="{{ route('bahan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Stok Bahan</span>
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Pengeluaran</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Laporan</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#A9C4B5] hover:bg-[#2E6B52]/50 hover:text-white transition">
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
            <div class="bg-[#2E6B52] p-3 rounded-xl flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#F6EFD8] text-[#1F4D3A] flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'MF', 0, 2)) }}
                    </div>
                    <div class="truncate flex-1 min-w-0">
                        <p class="font-bold text-xs text-white truncate">{{ auth()->user()->nama ?? auth()->user()->name ?? 'M. Fikri Firdaus' }}</p>
                        <p class="text-[11px] text-[#BFD3C7] truncate">{{ auth()->user()->peran ?? 'Admin / Owner' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline shrink-0">
                    @csrf
                    <button type="submit" class="text-[#BFD3C7] hover:text-white p-1 rounded-lg transition" title="Keluar / Logout">
                        <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content (Sesuai Figma Node 7:826) -->
    <main class="flex-1 p-5 sm:p-7 md:p-8 overflow-y-auto">
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
                        class="bg-[#143D2B] hover:bg-[#0E2C1F] text-white px-5 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold inline-flex items-center gap-2 shadow-sm transition cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Meja</span>
                </button>
            </div>
        </div>

        <!-- Grid Kartu Meja (Responsive: Mobile 1-col, Tablet 2-col, Desktop 4-col) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
            @forelse($tables as $table)
                <div class="bg-white border border-[#EBE6D8] rounded-[20px] p-4 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <!-- Top Section: QR Box -->
                    <div>
                        <div class="bg-[#FAF3E5] rounded-[16px] h-[148px] flex items-center justify-center p-4 mb-3.5 relative overflow-hidden group">
                            <!-- Crisp QR Code Icon / Vector SVG (matches Figma A7 Meja & QR Code) -->
                            <div class="w-20 h-20 text-[#2D3748] flex items-center justify-center">
                                <svg class="w-16 h-16" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <!-- Top-Left Finder Box -->
                                    <rect x="8" y="8" width="16" height="16" rx="4" />
                                    <rect x="13" y="13" width="6" height="6" rx="1.5" fill="currentColor" />
                                    <!-- Top-Right Finder Box -->
                                    <rect x="40" y="8" width="16" height="16" rx="4" />
                                    <rect x="45" y="13" width="6" height="6" rx="1.5" fill="currentColor" />
                                    <!-- Bottom-Left Finder Box -->
                                    <rect x="8" y="40" width="16" height="16" rx="4" />
                                    <rect x="13" y="45" width="6" height="6" rx="1.5" fill="currentColor" />
                                    <!-- QR Pattern Elements -->
                                    <path d="M40 40h4v6h-4z" fill="currentColor" />
                                    <path d="M48 40h6v4h-6z" fill="currentColor" />
                                    <path d="M44 50h10v4h-10z" fill="currentColor" />
                                </svg>
                            </div>

                            <!-- Overlay Quick Scan Link on hover -->
                            <a href="{{ route('cart', ['table' => $table->table_number]) }}" target="_blank" class="absolute inset-0 bg-[#143D2B]/85 text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition p-2 text-center text-xs font-semibold rounded-[16px]">
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
                                        {{ $table->status === 'aktif' ? 'bg-[#E3F2E9] text-[#1E7446] hover:bg-[#d0ebd9]' : 'bg-[#FCE8E6] text-[#C53929] hover:bg-[#fad3cf]' }}">
                                    {{ ucfirst($table->status) }}
                                </button>
                            </form>
                        </div>

                        <!-- Token Text -->
                        <p class="text-[11px] text-[#8C988F] font-normal mb-3">
                            Token: <span class="font-mono text-gray-600">{{ $table->token }}</span>
                        </p>
                    </div>

                    <!-- Action Buttons Row (Unduh & Cetak) -->
                    <div class="flex items-center gap-2 pt-2.5 border-t border-gray-100/80">
                        <a href="{{ route('admin.tables.download-qr', $table->id) }}" 
                           class="flex-1 bg-white border border-[#E6E0CC] hover:bg-gray-50 text-[#1B2B24] py-1.5 px-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition text-center shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-[#1B2B24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh</span>
                        </a>

                        <a href="{{ route('admin.tables.print', $table->id) }}" 
                           target="_blank"
                           class="flex-1 bg-white border border-[#E6E0CC] hover:bg-gray-50 text-[#1B2B24] py-1.5 px-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition text-center shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-[#1B2B24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <span class="w-8 h-8 rounded-lg bg-[#143D2B] text-white font-semibold flex items-center justify-center">{{ $page }}</span>
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
