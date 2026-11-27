<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR - {{ $table->name }} | Cafetaria De Barrio</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        @media print {
            @page {
                size: auto;
                margin: 10mm;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-wrapper {
                min-height: auto !important;
                padding: 0 !important;
            }
            .print-card {
                box-shadow: none !important;
                border: 2.5px solid #1F4D3A !important;
                page-break-inside: avoid;
                margin: 0 auto !important;
            }
        }
    </style>
</head>
<body class="bg-[#F0EDE6] min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 antialiased print-wrapper">

    @php
        $displayName = trim($table->name);
        if (!str_starts_with(strtolower($displayName), 'meja')) {
            $displayLabel = 'MEJA ' . strtoupper($displayName);
        } else {
            $displayLabel = strtoupper($displayName);
        }
    @endphp

    <!-- Top Action Bar (Tersembunyi otomatis saat dicetak) -->
    <div class="no-print mb-6 flex flex-wrap items-center justify-center gap-2.5 max-w-md w-full">
        <button onclick="window.print()" class="px-5 py-2.5 bg-[#1F4D3A] hover:bg-[#143529] text-white rounded-xl text-xs sm:text-sm font-semibold shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Kartu Meja</span>
        </button>

        <a href="{{ $qrUrl }}" target="_blank" class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-[#1F4D3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Uji Buka URL</span>
        </a>

        <a href="{{ route('admin.tables.index') }}" class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs transition cursor-pointer">
            Kembali
        </a>
    </div>

    <!-- Kartu Akrilik / Meja Stand (Siap Cetak) -->
    <div class="print-card bg-white border-2 border-[#1F4D3A] rounded-[32px] p-6 sm:p-8 max-w-[340px] sm:max-w-[360px] w-full text-center shadow-xl relative overflow-hidden">
        
        <!-- Ornamen Header Brand -->
        <div class="flex flex-col items-center">
            <!-- Icon Logo Lingkaran -->
            <div class="w-16 h-16 rounded-full bg-[#F6EFD8] border border-[#E6E0CC] overflow-hidden flex items-center justify-center mb-3 shadow-2xs">
                <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafeteria De Barrio" class="w-full h-full object-cover">
            </div>

            <!-- Nama Cafe -->
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#1F4D3A] tracking-tight leading-tight">
                Cafetaria De Barrio
            </h1>
            <p class="text-[11px] sm:text-xs text-gray-500 font-semibold tracking-wider uppercase mt-0.5">
                Pesan Mandiri Dari Meja
            </p>

            <!-- Badge Meja -->
            <div class="mt-3.5 mb-2 inline-flex items-center gap-1.5 bg-[#DDEEE3] text-[#1F4D3A] px-5 py-1.5 rounded-full font-bold text-xs sm:text-sm tracking-wide shadow-2xs border border-[#1F4D3A]/15">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span>{{ $displayLabel }}</span>
            </div>
        </div>

        <!-- Frame QR Code (Bersih, Rapi, Sempurna Tanpa Tumpukan) -->
        <div class="bg-[#FBF8EE] p-5 rounded-2xl border border-[#E6E0CC] my-4 mx-auto w-48 h-48 sm:w-52 sm:h-52 flex items-center justify-center shadow-inner">
            <div class="w-38 h-38 sm:w-42 sm:h-42 flex items-center justify-center">
                {!! $qrSvg !!}
            </div>
        </div>

        <!-- Panduan Pemindaian -->
        <div class="space-y-1">
            <p class="text-xs sm:text-sm font-bold text-[#1B2B24]">
                Arahkan Kamera HP ke QR Code
            </p>
            <p class="text-[11px] text-gray-500 leading-relaxed px-2">
                Pilih menu favoritmu dan kirim pesanan langsung dari tempat duduk tanpa perlu antre ke kasir.
            </p>
        </div>

        <!-- Garis Pemisah & Footer Token -->
        <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-400">
            <span>Token: <strong class="font-mono text-gray-600">{{ $table->token }}</strong></span>
            <span class="text-[#1F4D3A] font-semibold">De Barrio Cafe</span>
        </div>
    </div>

</body>
</html>
