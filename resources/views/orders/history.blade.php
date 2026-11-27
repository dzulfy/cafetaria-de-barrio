@extends('layouts.pelanggan')

@section('title', 'Riwayat Pesanan')

@section('content')
@php
    $tableNumber = $tableNumber ?? session('active_table', '05');
    $activeStatus = $activeStatus ?? 'semua';
    $routeName = request()->route()?->getName() === 'pelanggan.beranda' ? 'pelanggan.beranda' : 'orders.history';
@endphp

<div class="px-5 pt-5 pb-6 space-y-4">
    {{-- Top Bar Title (Figma 3:96) --}}
    <div class="flex items-center justify-between">
        <h1 class="text-[22px] font-bold text-ink tracking-tight">Pesanan Saya</h1>
        <span class="rounded-full bg-brand-100 px-3 py-1 text-[11px] font-semibold text-brand-900">
            Meja {{ $tableNumber }}
        </span>
    </div>

    {{-- Status Filter Chips (Figma 3:105) --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        @foreach (['semua' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $key => $label)
            <a href="{{ route($routeName, ['table' => $tableNumber, 'status' => $key]) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition {{ $activeStatus === $key ? 'bg-brand-900 text-white shadow-2xs' : 'bg-white border border-line text-ink hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Orders List (Figma 3:158) --}}
    <div class="space-y-3">
        @forelse ($orders as $order)
            @php
                $badgeClasses = match ($order->status) {
                    'menunggu' => 'bg-warn-bg text-warn-fg',
                    'diproses' => 'bg-blue-50 text-blue-700',
                    'selesai' => 'bg-ok-bg text-brand-900',
                    default => 'bg-danger-bg text-danger',
                };
                $itemsSummary = $order->items->map(fn ($item) => "{$item->quantity}x {$item->menu_name}")->implode(', ');
                $totalAmount = $order->total_price ?: $order->total_amount ?: 0;
            @endphp
            <a href="{{ route('orders.status', ['order_code' => $order->order_code]) }}"
               class="block bg-white rounded-2xl p-4 border border-line shadow-2xs hover:border-brand-900/40 transition">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-bold text-sm text-ink">{{ $order->order_code }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeClasses }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <p class="text-[11px] text-muted mt-1">
                    {{ $order->created_at?->format('d M Y, H.i') }}
                </p>

                <p class="text-xs text-ink mt-2 line-clamp-2">
                    {{ $itemsSummary ?: 'Pesanan Meja ' . $tableNumber }}
                </p>

                <div class="border-t border-line my-2.5"></div>

                <div class="flex items-center justify-between text-xs">
                    <span class="text-muted font-medium">Total</span>
                    <span class="font-bold text-sm text-brand-900">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-2xl p-8 text-center border border-line shadow-2xs space-y-2">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-brand-100 text-brand-900">
                    <x-lucide.coffee class="size-6" />
                </div>
                <p class="text-sm font-bold text-ink">Belum ada pesanan</p>
                <p class="text-xs text-muted">
                    Yuk pilih menu favoritmu dan kirim pesanan langsung dari meja.
                </p>
                <div class="pt-2">
                    <a href="{{ route('customer.menu', ['table' => $tableNumber]) }}"
                       class="inline-flex items-center justify-center rounded-xl bg-brand-900 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-700 transition">
                        Lihat Menu
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
