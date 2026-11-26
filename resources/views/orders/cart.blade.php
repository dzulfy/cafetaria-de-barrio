@extends('layouts.app')

@section('title', '03 Keranjang - Cafetaria De Barrio')

@section('content')
<div class="max-w-6xl mx-auto">

    @if(request()->get('view') === 'mockup')
        <!-- DUAL SCREEN MOCKUP VIEW (Identik dengan Gambar Unggahan User) -->
        <div class="mb-4 text-center">
            <span class="inline-block bg-[#1D4939] text-white text-xs px-3.5 py-1.5 rounded-full font-semibold shadow-xs">
                ✨ Mode Perbandingan 2 Layar (03 Keranjang & 04 Status Pesanan)
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start justify-center">
            <!-- Screen 1: 03 Keranjang -->
            <div>
                <div class="text-gray-400 font-semibold text-lg mb-3 tracking-wide px-1">03 Keranjang</div>
                @include('orders.partials.cart_card', ['inMockup' => true])
            </div>

            <!-- Screen 2: 04 Status Pesanan -->
            <div>
                <div class="text-gray-400 font-semibold text-lg mb-3 tracking-wide px-1">04 Status Pesanan</div>
                @php
                    $sampleOrder = \App\Models\Order::with('items')->where('order_code', 'DB-0124')->first() ?? \App\Models\Order::with('items')->latest()->first();
                @endphp
                @if($sampleOrder)
                    @include('orders.partials.status_card', ['order' => $sampleOrder, 'inMockup' => true])
                @else
                    <div class="bg-white rounded-3xl p-6 text-center text-gray-500 shadow-md">
                        Belum ada pesanan aktif. Silakan kirim pesanan dari keranjang terlebih dahulu.
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- RESPONSIVE VIEW (Mobile, Tablet, Desktop) -->
        <div class="flex flex-col items-center">
            <div class="w-full max-w-md lg:max-w-4xl mb-3 flex items-center justify-between text-gray-500 font-semibold text-xs sm:text-sm px-1">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    <span>03 Keranjang Pemesanan</span>
                </span>
                <span class="text-xs text-gray-400 font-normal">Tampilan Web, Tablet & Mobile</span>
            </div>

            <div class="w-full max-w-md lg:max-w-4xl">
                @include('orders.partials.cart_card', ['inMockup' => false])
            </div>
        </div>
    @endif

</div>
@endsection
