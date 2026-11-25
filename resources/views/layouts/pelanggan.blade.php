<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Pelanggan De Barrio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F0EDE4] font-sans text-ink antialiased">
<div class="mx-auto flex min-h-screen max-w-[390px] flex-col bg-cream-50 pb-20 shadow-lg">
    @if (session('success'))
        <p class="m-4 rounded-xl bg-ok-bg px-4 py-2.5 text-xs font-medium text-brand-900 shadow-sm">{{ session('success') }}</p>
    @endif
    @if (session('error'))
        <p class="m-4 rounded-xl bg-danger-bg px-4 py-2.5 text-xs font-medium text-danger shadow-sm">{{ session('error') }}</p>
    @endif

    @yield('content')

    {{-- Bottom Navigation Pelanggan --}}
    <nav class="fixed inset-x-0 bottom-0 mx-auto flex max-w-[390px] border-t border-line bg-white py-2.5 shadow-md">
        <a href="{{ route('pelanggan.beranda') }}" class="flex flex-1 flex-col items-center gap-1 {{ request()->routeIs('pelanggan.beranda') ? 'font-bold text-brand-900' : 'text-muted' }}">
            <x-lucide.coffee class="size-5" />
            <span class="text-[11px]">Pesanan</span>
        </a>
        <a href="{{ route('pelanggan.akun') }}" class="flex flex-1 flex-col items-center gap-1 {{ request()->routeIs('pelanggan.akun') ? 'font-bold text-brand-900' : 'text-muted' }}">
            <x-lucide.user class="size-5" />
            <span class="text-[11px]">Akun</span>
        </a>
    </nav>
</div>
</body>
</html>
