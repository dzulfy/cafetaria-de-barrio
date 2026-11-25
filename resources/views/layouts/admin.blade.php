<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Stok Bahan') - De Barrio</title>

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
    </style>
</head>
<body class="min-h-screen text-[#1E2922] bg-[#FAF8F4] flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#184233] text-white flex flex-col justify-between p-6 shrink-0 min-h-screen select-none">
        <div>
            <!-- Header Brand -->
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

            <!-- Navigation Links -->
            <nav class="mt-8 space-y-1.5">
                <!-- Dashboard -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D] transition">
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

                <!-- Stok Bahan (Active) -->
                <a href="{{ route('bahan.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold bg-[#285744] text-white shadow-inner transition">
                    <svg class="w-5 h-5 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                    <span>Stok Bahan</span>
                </a>

                <!-- Pengeluaran -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-[#8DAAA0] hover:text-white hover:bg-[#1E4D3D] transition">
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
        <div class="bg-[#224E3D] rounded-2xl p-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#E5DFD3] text-[#184233] font-bold text-xs flex items-center justify-center tracking-wider">
                    MF
                </div>
                <div>
                    <h2 class="text-white font-semibold text-xs leading-tight">M. Fikri Firdaus</h2>
                    <p class="text-[#8DAAA0] text-[11px] font-medium">Admin / Owner</p>
                </div>
            </div>
            <button type="button" class="text-[#8DAAA0] hover:text-white transition p-1" title="Keluar">
                <svg class="w-4 h-4 stroke-current stroke-2 fill-none" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 px-8 lg:px-12 py-8 min-h-screen overflow-x-hidden">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
