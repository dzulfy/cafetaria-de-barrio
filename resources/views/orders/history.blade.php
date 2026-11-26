@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Cafetaria De Barrio')

@section('content')
<div class="max-w-5xl mx-auto flex flex-col items-center">
    <div class="w-full max-w-[420px] mb-3 flex items-center justify-between text-gray-400 font-semibold text-sm px-1">
        <span>Riwayat Pesanan</span>
        <a href="{{ route('cart') }}" class="text-xs text-[#1D4939] hover:underline font-medium">
            ← Kembali ke Keranjang
        </a>
    </div>

    <div class="w-full max-w-[420px] bg-[#F9F7F2] rounded-[32px] p-5 sm:p-6 shadow-xl border border-gray-200/70">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200/80 mb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('cart') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-2xs flex items-center justify-center text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-lg font-bold text-gray-900 tracking-tight">Riwayat Pesanan</h1>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-[#D8EBE1] text-[#1D4939] rounded-lg">
                Meja {{ $tableNumber }}
            </span>
        </div>

        <div class="space-y-3">
            @forelse($orders as $order)
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs hover:border-[#1D4939]/30 transition">
                    <div class="flex items-start justify-between gap-2 mb-2.5">
                        <div>
                            <span class="font-bold text-sm text-gray-900">{{ $order->order_code }}</span>
                            <span class="text-[11px] text-gray-400 block">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                            {{ $order->status === 'menunggu' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                            {{ $order->status === 'diproses' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                            {{ $order->status === 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="text-xs text-gray-600 space-y-1 mb-3 bg-gray-50/60 p-2.5 rounded-xl">
                        @foreach($order->items as $item)
                            <div class="flex justify-between">
                                <span>{{ $item->quantity }}x {{ $item->menu_name }}</span>
                                <span class="text-gray-700 font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <div class="text-xs font-bold text-[#1D4939]">
                            Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </div>
                        <a href="{{ route('orders.status', ['order_code' => $order->order_code]) }}" 
                           class="text-xs font-semibold text-[#1D4939] hover:underline flex items-center gap-1">
                            Status Pesanan →
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center text-gray-400 text-sm border border-gray-100">
                    <p class="mb-3">Belum ada riwayat pesanan untuk Meja {{ $tableNumber }}.</p>
                    <a href="{{ route('cart') }}" class="inline-block px-4 py-2 bg-[#1D4939] text-white rounded-xl text-xs font-semibold">
                        Buat Pesanan Baru
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
