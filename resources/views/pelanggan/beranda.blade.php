@extends('layouts.pelanggan')
@section('title', 'Pesanan Saya')

@section('content')
<div class="p-5 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-[22px] font-bold text-ink">Riwayat Pesanan</h1>
            <p class="text-xs text-muted">Halo, {{ auth()->user()->nama ?? auth()->user()->name }}</p>
        </div>
        <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-900">
            Pelanggan
        </span>
    </div>

    <div class="rounded-2xl border border-line bg-white p-6 text-center shadow-sm">
        <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-brand-100 text-brand-900">
            <x-lucide.coffee class="size-6" />
        </div>
        <h3 class="mt-3 text-sm font-bold text-ink">Belum Ada Pesanan Aktif</h3>
        <p class="mt-1 text-xs text-muted leading-relaxed">
            Daftar pesanan Anda akan muncul di sini saat Anda melakukan pemesanan melalui QR meja.
        </p>
    </div>
</div>
@endsection
