<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Kasir De Barrio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 font-sans text-ink antialiased">
<div class="flex min-h-screen">
    {{-- Rail Kasir (96px) --}}
    <aside class="flex w-24 shrink-0 flex-col items-center gap-4 bg-brand-900 py-6">
        <span class="flex size-12 items-center justify-center rounded-full bg-cream text-brand-900 shadow">
            <x-lucide.coffee class="size-6" />
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

        <div class="mt-auto flex flex-col items-center gap-2">
            <span class="flex size-8 items-center justify-center rounded-full bg-cream text-xs font-bold text-brand-900">
                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 2)) }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar" class="rounded-xl p-2 text-[#BFD3C7] transition hover:text-white">
                    <x-lucide.log-out class="size-5" />
                </button>
            </form>
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
</body>
</html>
