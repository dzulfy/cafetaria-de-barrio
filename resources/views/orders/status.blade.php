@extends('layouts.app')

@section('title', '04 Status Pesanan - Cafetaria De Barrio')

@section('content')
<div class="max-w-4xl mx-auto flex flex-col items-center">
    <div class="w-full max-w-md lg:max-w-2xl mb-3 flex items-center justify-between text-gray-500 font-semibold text-xs sm:text-sm px-1">
        <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
            <span>04 Status Pesanan (Live Tracking)</span>
        </span>
        <a href="{{ route('cart', ['view' => 'mockup']) }}" class="text-xs text-[#1D4939] hover:underline font-semibold">
            Mode 2 Layar Berdampingan →
        </a>
    </div>

    <div class="w-full max-w-md lg:max-w-2xl">
        @include('orders.partials.status_card', ['order' => $order, 'inMockup' => false])
    </div>
</div>
@endsection
