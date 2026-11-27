<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Cafetaria De Barrio</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-de-barrio.jpg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Instrument Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#184233',
                            active: '#285744',
                            card: '#224E3D',
                            text: '#8DAAA0',
                            cream: '#FAF8F4',
                            sage: '#EBF2EE',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #FAF8F4;
        }
        #sidebarMenu {
            overflow: hidden !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        #sidebarMenu::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F1F4F2;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #CBD7D0;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9BB0A3;
        }
    </style>
</head>
<body class="min-h-screen text-[#1E2922] bg-[#FAF8F4] flex flex-col lg:flex-row antialiased">

    <!-- Mobile Top Navigation Bar -->
    <header class="lg:hidden sticky top-0 z-30 bg-[#184233] text-white px-4 py-3 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="toggleSidebar()"
                class="p-2 rounded-xl text-white hover:bg-[#224E3D] transition focus:outline-none focus:ring-2 focus:ring-white/20"
                aria-label="Buka Menu"
            >
                <svg class="w-6 h-6 stroke-current stroke-2 fill-none" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-[#FAF8F4] overflow-hidden border border-white/25 flex items-center justify-center shadow-xs shrink-0">
                    <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafetaria De Barrio" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="font-bold text-sm sm:text-base leading-tight tracking-tight text-white">Cafetaria De Barrio</h1>
                    <p class="text-[10px] text-[#8DAAA0] font-medium leading-none">Panel Admin</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-[#E5DFD3] text-[#184233] font-bold text-xs flex items-center justify-center tracking-wider shadow-xs">
                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'AD', 0, 2)) }}
            </div>
            @if(auth()->check())
            <button type="button"
                    onclick="openLogoutModal()"
                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/25 hover:bg-rose-600 border border-rose-300/40 text-rose-100 hover:text-white text-xs font-bold flex items-center gap-1.5 transition cursor-pointer"
                    title="Keluar / Logout">
                <svg class="w-4 h-4 stroke-current fill-none" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Keluar</span>
            </button>
            @endif
        </div>
    </header>

    <!-- Overlay Backdrop -->
    <div
        id="sidebarBackdrop"
        onclick="toggleSidebar()"
        class="fixed inset-0 bg-black/50 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"
        aria-hidden="true"
    ></div>

    <!-- Sidebar -->
    <aside
        id="sidebarMenu"
        class="fixed inset-y-0 left-0 z-50 w-72 lg:w-64 bg-[#184233] text-white flex flex-col justify-between p-5 lg:p-6 shrink-0 h-screen select-none transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:overflow-hidden shadow-2xl lg:shadow-none overflow-hidden"
    >
        <div class="flex flex-col h-full justify-between">
            <div>
                <!-- Header Brand -->
                <div class="flex items-center justify-between shrink-0 mb-6 lg:mb-7">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#FAF8F4] overflow-hidden border border-white/25 flex items-center justify-center shadow-sm shrink-0">
                            <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafetaria De Barrio" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h1 class="font-bold text-base leading-tight tracking-tight text-white">Cafetaria De Barrio</h1>
                            <p class="text-xs text-[#8DAAA0] font-medium">Panel Admin</p>
                        </div>
                    </div>

                    <button
                        type="button"
                        onclick="toggleSidebar()"
                        class="lg:hidden p-1.5 rounded-lg text-[#8DAAA0] hover:text-white hover:bg-[#224E3D] transition"
                        title="Tutup Menu"
                    >
                        <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-1 sm:space-y-1.5 shrink-0">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('dashboard') || request()->is('dashboard') || request()->is('admin/dashboard') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                        <svg class="w-5 h-5 stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Menu & Kategori -->
                    <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.menus.*') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                        <svg class="w-5 h-5 stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                            <line x1="6" y1="1" x2="6" y2="4"></line>
                            <line x1="10" y1="1" x2="10" y2="4"></line>
                            <line x1="14" y1="1" x2="14" y2="4"></line>
                        </svg>
                        <span>Menu & Kategori</span>
                    </a>

                    <!-- Stok Bahan -->
                    <a href="{{ route('bahan.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('bahan.*') || request()->is('bahan*') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                        <svg class="w-5 h-5 stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m7.5 4.27 9 5.15"></path>
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                            <path d="m3.3 7 8.7 5 8.7-5"></path>
                            <path d="M12 22V12"></path>
                        </svg>
                        <span>Stok Bahan</span>
                    </a>

                    <!-- Pengeluaran -->
                    <a href="{{ route('pengeluaran.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('pengeluaran.*') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                        <svg class="w-5 h-5 stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                            <line x1="2" y1="10" x2="22" y2="10"></line>
                        </svg>
                        <span>Pengeluaran</span>
                    </a>

                    <!-- Pengguna -->
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.users.*') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                        <svg class="w-5 h-5 stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Pengguna</span>
                    </a>

                    <!-- Meja & QR -->
                    <a href="{{ route('admin.tables.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.tables.*') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                        <svg class="w-5 h-5 stroke-current stroke-2 fill-none shrink-0" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                            <circle cx="17.5" cy="17.5" r="2.5"></circle>
                        </svg>
                        <span>Meja & QR</span>
                    </a>
                </nav>
            </div>

            <!-- User Profile Card & Logout -->
            <div class="bg-[#224E3D] rounded-2xl p-3 flex flex-col gap-2.5 shadow-xs mt-auto shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#E5DFD3] text-[#184233] font-bold text-xs flex items-center justify-center tracking-wider shrink-0">
                        {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'MF', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-white font-semibold text-xs leading-tight truncate">{{ auth()->user()->nama ?? auth()->user()->name ?? 'M. Fikri Firdaus' }}</h2>
                        <p class="text-[#8DAAA0] text-[11px] font-medium truncate">{{ auth()->user()->peran ?? 'Admin / Owner' }}</p>
                    </div>
                </div>
                @if(auth()->check())
                <button type="button"
                        onclick="openLogoutModal()"
                        class="w-full py-2 px-3 rounded-xl bg-rose-500/20 hover:bg-rose-600 border border-rose-300/35 text-rose-100 hover:text-white text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer"
                        title="Keluar / Logout">
                    <svg class="w-[18px] h-[18px] stroke-current fill-none shrink-0" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Keluar</span>
                </button>
                @endif
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full min-w-0 px-4 sm:px-6 lg:px-10 py-6 sm:py-8 min-h-screen overflow-x-hidden">
        @if (session('success'))
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

        @if (session('error'))
            <div id="flash-error" class="mb-5 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm animate-fade-in">
                <span class="text-sm font-medium">{{ session('error') }}</span>
                <button onclick="document.getElementById('flash-error').remove()" class="text-rose-500 hover:text-rose-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                }, 10);
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebarMenu');
                if (sidebar && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) {
                    toggleSidebar();
                }
            }
        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;
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

    <x-logout-modal />

    @stack('scripts')
</body>
</html>
