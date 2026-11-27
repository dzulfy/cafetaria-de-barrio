<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Kasir Cafetaria De Barrio</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-de-barrio.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 font-sans text-ink antialiased">
<div class="flex min-h-screen">
    {{-- Rail Kasir (96px) --}}
    <aside class="flex w-24 shrink-0 flex-col items-center gap-4 bg-brand-900 py-6">
        <span class="flex size-12 items-center justify-center rounded-full bg-cream overflow-hidden border border-white/25 shadow">
            <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafetaria De Barrio" class="w-full h-full object-cover">
        </span>

        <div class="mt-4 flex flex-col items-center gap-3">
            <a href="{{ route('kasir.index') }}" class="flex flex-col items-center gap-1 rounded-xl bg-brand-700 p-2.5 text-white">
                <x-lucide.coffee class="size-5" />
                <span class="text-[11px] font-semibold">Kasir</span>
            </a>
            <a href="{{ route('password.edit') }}" title="Ubah Kata Sandi" class="flex flex-col items-center gap-1 rounded-xl p-2.5 text-[#A9C4B5] hover:text-white">
                <x-lucide.lock class="size-5" />
                <span class="text-[11px] font-semibold">Sandi</span>
            </a>
        </div>

        <div class="mt-auto flex flex-col items-center gap-3 w-full px-2">
            <span class="flex size-10 items-center justify-center rounded-full bg-cream text-xs font-bold text-brand-900 shadow-xs"
                  title="{{ auth()->user()->nama ?? auth()->user()->name ?? 'Kasir' }}">
                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 2)) }}
            </span>
            <button type="button"
                    onclick="openLogoutModal()"
                    title="Keluar / Logout"
                    class="w-14 py-2 rounded-2xl bg-rose-500/20 hover:bg-rose-600 border border-rose-300/40 text-rose-100 hover:text-white flex flex-col items-center justify-center gap-1 transition shadow-xs cursor-pointer">
                <svg class="w-5 h-5 stroke-current fill-none" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="text-[10px] font-bold leading-none">Keluar</span>
            </button>
        </div>
    </aside>

    {{-- Main Kasir Area --}}
    <main class="flex-1 p-8">
        @if (session('success'))
            <p class="mb-4 rounded-xl bg-ok-bg px-4 py-3 text-sm font-medium text-brand-900 shadow-sm">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="mb-4 rounded-xl bg-danger-bg px-4 py-3 text-sm font-medium text-danger shadow-sm">{{ session('error') }}</p>
        @endif
        @yield('content')
    </main>
</div>

<x-logout-modal />
</body>
</html>
