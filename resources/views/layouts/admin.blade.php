<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - De Barrio</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
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
        /* Custom scrollbar halus untuk tabel pada layar kecil */
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

    <!-- Mobile Top Navigation Bar (Hanya tampil di Layar Mobile / Tablet < lg) -->
    <header class="lg:hidden sticky top-0 z-30 bg-[#184233] text-white px-4 py-3 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-3">
            <!-- Tombol Hamburger Menu -->
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

            <!-- Brand Logo & Name Mobile -->
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-[#184233] shadow-xs">
                    <svg class="w-4 h-4 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                        <path d="M17 8h1a4 4 0 1 1 0 8h-1"></path>
                        <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-base leading-tight tracking-tight text-white">De Barrio</h1>
                    <p class="text-[10px] text-[#8DAAA0] font-medium leading-none">Panel Admin</p>
                </div>
            </div>
        </div>

        <!-- User Initials Avatar di Header Mobile -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-[#E5DFD3] text-[#184233] font-bold text-xs flex items-center justify-center tracking-wider shadow-xs">
                MF
            </div>
        </div>
    </header>

    <!-- Overlay Backdrop untuk Mobile Sidebar -->
    <div
        id="sidebarBackdrop"
        onclick="toggleSidebar()"
        class="fixed inset-0 bg-black/50 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"
        aria-hidden="true"
    ></div>

    <!-- Sidebar (Drawer Responsif di Mobile, Kolom Statis di Desktop/Laptop) -->
    <aside
        id="sidebarMenu"
        class="fixed inset-y-0 left-0 z-50 w-72 lg:w-64 bg-[#184233] text-white flex flex-col justify-between p-6 shrink-0 min-h-screen select-none transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:static lg:min-h-screen shadow-2xl lg:shadow-none overflow-y-auto"
    >
        <div>
            <!-- Header Brand & Tombol Tutup di Mobile -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#184233] shadow-sm">
                        <svg class="w-6 h-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 8h1a4 4 0 1 1 0 8h-1"></path>
                            <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"></path>
                            <line x1="6" y1="2" x2="6" y2="4"></line>
                            <line x1="10" y1="2" x2="10" y2="4"></line>
                            <line x1="14" y1="2" x2="14" y2="4"></line>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight tracking-tight text-white">De Barrio</h1>
                        <p class="text-xs text-[#8DAAA0] font-medium">Panel Admin</p>
                    </div>
                </div>

                <!-- Tombol Close Sidebar (Hanya di Layar Mobile) -->
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
            <nav class="mt-8 space-y-1.5">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('dashboard') || request()->is('/') || request()->is('dashboard') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                    <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Menu & Kategori -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D] transition">
                    <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                        <line x1="6" y1="1" x2="6" y2="4"></line>
                        <line x1="10" y1="1" x2="10" y2="4"></line>
                        <line x1="14" y1="1" x2="14" y2="4"></line>
                    </svg>
                    <span>Menu & Kategori</span>
                </a>

                <!-- Stok Bahan -->
                <a href="{{ route('bahan.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('bahan.*') || request()->is('bahan*') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                    <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                    <span>Stok Bahan</span>
                </a>

                <!-- Pengeluaran -->
                <a href="{{ route('pengeluaran.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('pengeluaran.*') ? 'font-semibold bg-[#285744] text-white shadow-inner' : 'font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D]' }} transition">
                    <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                    <span>Pengeluaran</span>
                </a>

                <!-- Laporan -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D] transition">
                    <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <span>Laporan</span>
                </a>

                <!-- Pengguna -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D] transition">
                    <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Pengguna</span>
                </a>

                <!-- Meja & QR -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D] transition">
                    <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                        <circle cx="17.5" cy="17.5" r="2.5"></circle>
                    </svg>
                    <span>Meja & QR</span>
                </a>
            </nav>
        </div>

        <!-- User Profile Card -->
        <div class="bg-[#224E3D] rounded-2xl p-3 flex items-center justify-between shadow-xs mt-8">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#E5DFD3] text-[#184233] font-bold text-xs flex items-center justify-center tracking-wider shrink-0">
                    MF
                </div>
                <div class="min-w-0">
                    <h2 class="text-white font-semibold text-xs leading-tight truncate">M. Fikri Firdaus</h2>
                    <p class="text-[#8DAAA0] text-[11px] font-medium truncate">Admin / Owner</p>
                </div>
            </div>
            <button type="button" class="text-[#8DAAA0] hover:text-white transition p-1 shrink-0" title="Keluar">
                <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full min-w-0 px-4 sm:px-6 lg:px-10 py-6 sm:py-8 min-h-screen overflow-x-hidden">
        @yield('content')
    </main>

    <!-- Script Drawer Sidebar Mobile -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (sidebar.classList.contains('-translate-x-full')) {
                // Buka sidebar
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                }, 10);
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            } else {
                // Tutup sidebar
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
