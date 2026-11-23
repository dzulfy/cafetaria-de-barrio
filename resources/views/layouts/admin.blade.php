<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>De Barrio - @yield('title', 'Panel Admin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-[#f7f4ed] text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Notification & Mode Switcher Bar -->
    <header class="bg-[#122820] text-emerald-100 text-xs px-4 py-2 flex items-center justify-between border-b border-[#214336] z-30">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="font-medium text-white">De Barrio Cafetaria</span>
            <span class="text-emerald-300/70 hidden sm:inline">• Database SQLite Connected</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('customer.menu') }}" class="inline-flex items-center gap-1.5 bg-[#20493a] hover:bg-[#2c614d] text-white px-3 py-1 rounded-full transition-all text-xs font-medium border border-emerald-600/30">
                <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>Lihat Tampilan Menu Pelanggan (Mobile)</span>
            </a>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        <!-- Sidebar matching Image 1 -->
        <aside class="w-64 bg-[#1b3d32] text-white flex flex-col flex-shrink-0 z-20 transition-all duration-300">
            <!-- App Brand Header -->
            <div class="p-6 flex items-center gap-3 border-b border-[#285244]/60">
                <div class="w-10 h-10 rounded-full border-2 border-emerald-400/40 bg-[#254d40] flex items-center justify-center text-emerald-200 shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-base tracking-wide text-white leading-tight">De Barrio</h1>
                    <p class="text-xs text-emerald-300/80 font-normal">Panel Admin</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-emerald-100/75 hover:bg-[#254d40] hover:text-white transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-300/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Menu & Kategori (Active) -->
                <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-[#2d5648] text-white font-semibold shadow-sm text-sm border-l-4 border-emerald-400">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"></path>
                    </svg>
                    <span>Menu & Kategori</span>
                </a>

                <!-- Stok Bahan -->
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-emerald-100/75 hover:bg-[#254d40] hover:text-white transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-300/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Stok Bahan</span>
                </a>

                <!-- Pengeluaran -->
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-emerald-100/75 hover:bg-[#254d40] hover:text-white transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-300/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                    <span>Pengeluaran</span>
                </a>

                <!-- Laporan -->
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-emerald-100/75 hover:bg-[#254d40] hover:text-white transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-300/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Laporan</span>
                </a>

                <!-- Pengguna -->
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-emerald-100/75 hover:bg-[#254d40] hover:text-white transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-300/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Pengguna</span>
                </a>

                <!-- Meja & QR -->
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-emerald-100/75 hover:bg-[#254d40] hover:text-white transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-300/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Meja & QR</span>
                </a>
            </nav>

            <!-- Bottom Profile Card matching Image 1 -->
            <div class="p-3 m-3 rounded-2xl bg-[#254d40]/80 border border-[#2f5c4e]/50 flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#d6eadf] text-[#1b3d32] font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                        MF
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate">M. Fikri Firdaus</p>
                        <p class="text-[11px] text-emerald-300/80 truncate">Admin | Owner</p>
                    </div>
                </div>
                <button title="Keluar" class="p-1.5 text-emerald-200/80 hover:text-white hover:bg-[#1b3d32] rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-[#f7f4ed]">
            <!-- Flash Message Banner -->
            @if(session('success'))
            <div id="flash-banner" class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="document.getElementById('flash-banner').remove()" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-[#1b3d32] text-white border-emerald-500' : 'bg-red-800 text-white border-red-500';
            toast.className = `${bgClass} border px-4 py-3 rounded-xl shadow-lg flex items-center gap-2.5 text-sm font-medium pointer-events-auto transform transition-all duration-300 translate-y-2 opacity-0`;
            toast.innerHTML = `
                <svg class="w-4 h-4 text-emerald-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>${message}</span>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
    @stack('scripts')
</body>
</html>
