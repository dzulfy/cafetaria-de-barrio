@extends('layouts.app')

@section('title', 'Keranjang — De Barrio')

@section('content')
@if(request()->get('view') === 'mockup')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start justify-center">
        <div>
            <div class="text-muted font-semibold text-sm mb-3 px-1">03 Keranjang</div>
            <div class="bg-cream-50 rounded-[32px] shadow-xl border border-line overflow-hidden">
                @include('orders.partials.cart_card', ['inMockup' => true])
            </div>
        </div>
        <div>
            <div class="text-muted font-semibold text-sm mb-3 px-1">04 Status Pesanan</div>
            @php
                $sampleOrder = \App\Models\Order::with('items')->where('order_code', 'DB-0124')->first() ?? \App\Models\Order::with('items')->latest()->first();
            @endphp
            <div class="bg-cream-50 rounded-[32px] shadow-xl border border-line overflow-hidden">
                @if($sampleOrder)
                    @include('orders.partials.status_card', ['order' => $sampleOrder, 'inMockup' => true])
                @else
                    <div class="p-6 text-center text-muted text-sm">
                        Belum ada pesanan aktif.
                    </div>
                @endif
            </div>
        </div>
    </div>
@else
    @include('orders.partials.cart_card', ['inMockup' => false])
@endif
@endsection
