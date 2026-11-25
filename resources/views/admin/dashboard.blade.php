@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="space-y-1">
        <h1 class="text-[26px] font-bold text-ink">Dashboard</h1>
        <p class="text-[13px] text-muted">Selamat datang kembali, {{ auth()->user()->nama ?? auth()->user()->name }} (Admin / Owner)</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
        <a href="{{ route('admin.users.index') }}" class="group block rounded-[14px] border border-line bg-white p-6 shadow-sm transition hover:border-brand-900 hover:shadow">
            <div class="flex size-12 items-center justify-center rounded-xl bg-brand-100 text-brand-900 transition group-hover:bg-brand-900 group-hover:text-cream">
                <x-lucide.users class="size-6" />
            </div>
            <p class="mt-4 text-base font-bold text-ink">Kelola Pengguna</p>
            <p class="mt-1 text-xs text-muted">Atur akun admin, kasir, pelanggan, status aktif, dan hak akses.</p>
        </a>

        <div class="rounded-[14px] border border-line bg-white/60 p-6 opacity-75">
            <div class="flex size-12 items-center justify-center rounded-xl bg-[#EFEFEF] text-muted">
                <x-lucide.coffee class="size-6" />
            </div>
            <p class="mt-4 text-base font-bold text-ink">Menu & Kategori</p>
            <p class="mt-1 text-xs text-muted">Modul dikerjakan oleh anggota tim lain.</p>
        </div>

        <div class="rounded-[14px] border border-line bg-white/60 p-6 opacity-75">
            <div class="flex size-12 items-center justify-center rounded-xl bg-[#EFEFEF] text-muted">
                <x-lucide.chart-column class="size-6" />
            </div>
            <p class="mt-4 text-base font-bold text-ink">Laporan Penjualan</p>
            <p class="mt-1 text-xs text-muted">Modul dikerjakan oleh anggota tim lain.</p>
        </div>
    </div>
@endsection
