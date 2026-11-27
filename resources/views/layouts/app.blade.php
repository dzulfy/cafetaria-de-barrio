<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Warkop De Barrio')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="bg-[#ECEAE4] text-ink antialiased min-h-screen flex justify-center">
    <div class="w-full {{ request()->get('view') === 'mockup' ? 'max-w-5xl bg-transparent shadow-none p-4 sm:p-6' : 'max-w-[420px] bg-cream-50 shadow-xl' }} min-h-screen flex flex-col relative">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
