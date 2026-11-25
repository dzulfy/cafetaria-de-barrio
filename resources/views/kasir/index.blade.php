@extends('layouts.kasir')
@section('title', 'Kasir - Pesanan Baru')

@section('content')
    <div class="space-y-4">
        <div>
            <h1 class="text-[26px] font-bold text-ink">Pesanan Baru</h1>
            <p class="text-sm text-muted">Masuk sebagai <strong class="text-ink">{{ auth()->user()->nama ?? auth()->user()->name }}</strong> (Peran: Kasir)</p>
        </div>

        <div class="rounded-[14px] border border-line bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3 text-brand-900">
                <x-lucide.coffee class="size-6" />
                <h2 class="text-lg font-bold">Halaman Operasional Kasir</h2>
            </div>
            <p class="mt-2 text-sm text-muted">
                Antarmuka pemesanan dan pembayaran kasir terhubung dengan modul Kasir (dikerjakan oleh anggota tim lain). Modul User Management telah berhasil mengamankan akses ke rute ini khusus untuk peran <strong>Kasir</strong> dan <strong>Admin</strong>.
            </p>
            <div class="mt-4 flex items-center gap-3">
                <a href="{{ route('password.edit') }}" class="inline-flex items-center gap-2 rounded-xl border border-line bg-cream-50 px-4 py-2.5 text-xs font-semibold text-ink hover:border-brand-900">
                    <x-lucide.lock class="size-4 text-muted" />
                    <span>Ubah Kata Sandi Saya</span>
                </a>
            </div>
        </div>
    </div>
@endsection
