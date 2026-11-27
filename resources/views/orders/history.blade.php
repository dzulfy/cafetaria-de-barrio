@extends('layouts.pelanggan')

@section('title', 'Riwayat Pesanan')

@section('content')
@php
    $tableNumber = $tableNumber ?? session('active_table');
    $tableParams = $tableNumber ? ['table' => $tableNumber] : [];
    $activeStatus = $activeStatus ?? 'semua';
    $routeName = request()->route()?->getName() === 'pelanggan.beranda' ? 'pelanggan.beranda' : 'orders.history';
    $loggedCustomer = auth()->user();
    $scopeLabel = $tableNumber
        ? 'Meja ' . $tableNumber
        : ($loggedCustomer && ! $loggedCustomer->hasRole('admin', 'kasir')
            ? 'Akun: ' . ($loggedCustomer->nama ?? $loggedCustomer->name ?? 'Pelanggan')
            : 'Sesi Perangkat Ini');
@endphp

<div class="px-5 md:px-6 pt-5 md:pt-8 pb-6 space-y-5">
    {{-- Top Bar Title --}}
    <div class="flex items-center justify-between animate-fade-up">
        <h1 class="text-[22px] sm:text-2xl font-bold text-ink tracking-tight">Pesanan Saya</h1>
        <span class="rounded-full {{ $tableNumber ? 'bg-brand-100 text-brand-900' : 'bg-white border border-line text-muted' }} px-3 py-1 text-[11px] font-semibold">
            {{ $scopeLabel }}
        </span>
    </div>

    {{-- Status Filter Chips --}}
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
        @foreach (['semua' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $key => $label)
            <a href="{{ route($routeName, array_filter(['status' => $key])) }}"
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
                $paymentStatusLabel = match ($order->payment_status) {
                    'paid' => 'Lunas',
                    'pending' => 'Menunggu Konfirmasi',
                    'expired' => 'Kedaluwarsa',
                    'failed' => 'Gagal',
                    default => 'Belum Bayar',
                };
                $paymentStatusClass = match ($order->payment_status) {
                    'paid' => 'bg-ok-bg text-brand-900',
                    'pending' => 'bg-blue-50 text-blue-700',
                    'expired', 'failed' => 'bg-danger-bg text-danger',
                    default => 'bg-amber-50 text-amber-700',
                };
                $totalAmount = (int) ($order->total_amount ?: $order->total_price ?: 0);
                $itemsSubtotal = (int) $order->items->sum('subtotal');
                $orderSubtotal = (int) ($order->subtotal ?: $itemsSubtotal ?: $totalAmount);
                $orderTable = trim((string) preg_replace('/^Meja\s+/i', '', (string) ($order->table_number ?? '')));
            @endphp
            <a href="{{ route('orders.status', ['order_code' => $order->order_code]) }}"
               style="animation-delay: {{ min($loop->index * 45, 270) }}ms;"
               class="group flex flex-col justify-between bg-white rounded-2xl p-4 border border-line shadow-2xs hover:shadow-md hover:-translate-y-1 hover:border-brand-900/40 active:scale-[0.99] transition-all duration-200 animate-fade-up">
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="font-bold text-sm text-ink">{{ $order->order_code }}</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-cream-50 border border-line text-muted">
                                {{ $order->payment_method === 'tunai' ? 'Cash / Tunai' : 'QRIS / Transfer' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $paymentStatusClass }}">
                                {{ $paymentStatusLabel }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeClasses }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <p class="text-[11px] text-muted mt-1.5">
                        {{ $order->created_at?->format('d M Y, H.i') }}@if($orderTable) &bull; Meja {{ $orderTable }}@endif
                    </p>

                    <div class="mt-3 space-y-1.5">
                        @forelse ($order->items as $item)
                            <div class="flex items-center justify-between gap-2 text-xs text-ink">
                                <span class="truncate">{{ $item->quantity }}x {{ $item->menu_name }}</span>
                                <span class="font-medium text-muted shrink-0">Rp {{ number_format((int) $item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-muted">
                                {{ $orderTable ? 'Pesanan Meja ' . $orderTable : 'Pesanan Cafetaria De Barrio' }}
                            </p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="border-t border-line my-3"></div>

                    <div class="space-y-1 text-xs">
                        <div class="flex items-center justify-between text-[11px] text-muted">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($orderSubtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted font-medium">Total Pembayaran</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm text-brand-900">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                                <span class="text-muted transition-transform duration-200 group-hover:translate-x-0.5">&rarr;</span>
                            </div>
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
