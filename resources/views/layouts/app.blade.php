<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cafetaria De Barrio')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .bg-pattern {
            background-color: #ECEAE4;
            background-image: radial-gradient(#D5D2C8 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-pattern text-gray-800 antialiased min-h-screen flex flex-col">
    <!-- Top Global Switcher Bar for Review / Navigation -->
    <header class="bg-white/90 backdrop-blur-md border-b border-gray-200/80 sticky top-0 z-50 py-2.5 px-4 shadow-2xs">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('cart') }}" class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-[#1D4939] flex items-center justify-center text-white font-bold text-xs shadow-xs">
                        DB
                    </div>
                    <span class="font-bold text-gray-900 tracking-tight text-sm">Cafetaria De Barrio</span>
                </a>
                <span class="hidden sm:inline-block text-gray-300">|</span>
                <span class="hidden sm:inline-block text-gray-500 font-medium">Pemesanan Mandiri Meja</span>
            </div>
            
            <nav class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl">
                <a href="{{ route('cart') }}" class="px-3 py-1.5 rounded-lg font-medium transition {{ request()->routeIs('cart*') && !request()->has('view') ? 'bg-white text-[#1D4939] shadow-2xs font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                    03 Keranjang
                </a>
                <a href="{{ route('orders.status', ['order_code' => 'DB-0124']) }}" class="px-3 py-1.5 rounded-lg font-medium transition {{ request()->routeIs('orders.status*') ? 'bg-white text-[#1D4939] shadow-2xs font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                    04 Status Pesanan
                </a>
                <a href="{{ route('orders.history') }}" class="px-3 py-1.5 rounded-lg font-medium transition {{ request()->routeIs('orders.history') ? 'bg-white text-[#1D4939] shadow-2xs font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                    Riwayat
                </a>
                <a href="{{ route('cart', ['view' => 'mockup']) }}" class="hidden lg:inline-flex items-center gap-1 px-3 py-1.5 rounded-lg font-medium transition {{ request()->get('view') === 'mockup' ? 'bg-[#1D4939] text-white shadow-2xs font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    2 Layar (Figma)
                </a>
                <a href="{{ route('admin.tables.index') }}" class="px-3 py-1.5 rounded-lg font-bold bg-[#1F4D3A] text-white hover:bg-[#143529] shadow-xs transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>Admin A7 Meja</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="py-6 px-4 md:py-8 flex-1">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
