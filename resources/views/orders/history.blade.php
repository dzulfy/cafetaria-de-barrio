@extends('layouts.pelanggan')

@section('title', 'Riwayat Pesanan')

@section('content')
@php
    $tableNumber = $tableNumber ?? session('active_table');
    $tableParams = $tableNumber ? ['table' => $tableNumber] : [];
    $activeStatus = $activeStatus ?? 'semua';
    $routeName = request()->route()?->getName() === 'pelanggan.beranda' ? 'pelanggan.beranda' : 'orders.history';
@endphp

<div class="px-5 md:px-6 pt-5 md:pt-8 pb-6 space-y-5">
    {{-- Top Bar Title --}}
    <div class="flex items-center justify-between animate-fade-up">
        <h1 class="text-[22px] sm:text-2xl font-bold text-ink tracking-tight">Pesanan Saya</h1>
        <span class="rounded-full {{ $tableNumber ? 'bg-brand-100 text-brand-900' : 'bg-white border border-line text-muted' }} px-3 py-1 text-[11px] font-semibold">
            {{ $tableNumber ? 'Meja ' . $tableNumber : 'Semua Meja' }}
        </span>
    </div>

    {{-- Status Filter Chips --}}
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
        @foreach (['semua' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $key => $label)
            <a href="{{ route($routeName, array_filter(['table' => $tableNumber, 'status' => $key])) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition active:scale-95 {{ $activeStatus === $key ? 'bg-brand-900 text-white shadow-2xs' : 'bg-white border border-line text-ink hover:bg-brand-100/60' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Responsive Orders Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
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
                $orderTable = trim((string) preg_replace('/^Meja\s+/i', '', (string) ($order->table_number ?? '')));
            @endphp
            <a href="{{ route('orders.status', ['order_code' => $order->order_code]) }}"
               style="animation-delay: {{ min($loop->index * 45, 270) }}ms;"
               class="group flex flex-col justify-between bg-white rounded-2xl p-4 border border-line shadow-2xs hover:shadow-md hover:-translate-y-1 hover:border-brand-900/40 active:scale-[0.99] transition-all duration-200 animate-fade-up">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-bold text-sm text-ink">{{ $order->order_code }}</span>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $order->payment_status === 'paid' ? 'bg-ok-bg text-brand-900' : 'bg-amber-50 text-amber-700' }}">
                                {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeClasses }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <p class="text-[11px] text-muted mt-1">
                        {{ $order->created_at?->format('d M Y, H.i') }}@if($orderTable) &bull; Meja {{ $orderTable }}@endif
                    </p>

                    <p class="text-xs text-ink mt-2.5 line-clamp-2 leading-relaxed">
                        {{ $itemsSummary ?: ($orderTable ? 'Pesanan Meja ' . $orderTable : 'Pesanan Warkop') }}
                    </p>
                </div>

                <div>
                    <div class="border-t border-line my-3"></div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-muted font-medium">Total</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-bold text-sm text-brand-900">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                            <span class="text-muted transition-transform duration-200 group-hover:translate-x-0.5">&rarr;</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-10 text-center border border-line shadow-2xs space-y-2.5 animate-fade-up">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-brand-100 text-brand-900 animate-float-soft">
                    <x-lucide.coffee class="size-6" />
                </div>
                <p class="text-sm font-bold text-ink">Belum ada pesanan</p>
                <p class="text-xs text-muted max-w-xs mx-auto">
                    Yuk pilih menu favoritmu dan kirim pesanan langsung dari meja.
                </p>
                <div class="pt-2">
                    <a href="{{ route('customer.menu', $tableParams) }}"
                       class="inline-flex items-center justify-center rounded-xl bg-brand-900 px-5 py-2.5 text-xs font-semibold text-white hover:bg-brand-700 transition active:scale-95">
                        Lihat Menu
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
