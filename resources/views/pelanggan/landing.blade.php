@extends('layouts.guest')
@section('title', 'Selamat Datang')

@section('content')
<div class="flex min-h-screen flex-col items-center justify-center p-6 text-center">
    <div class="w-full max-w-md space-y-6 rounded-2xl border border-line bg-white p-8 shadow-sm">
        <div class="mx-auto flex size-20 items-center justify-center rounded-full bg-brand-900 text-cream shadow">
            <x-lucide.coffee class="size-10" />
        </div>

        <div class="space-y-2">
            <h1 class="text-3xl font-bold tracking-tight text-ink">Warkop De Barrio</h1>
            <p class="text-sm text-muted">Sistem Pemesanan & Manajemen Warkop Cafetaria De Barrio.</p>
        </div>

        <div class="space-y-3 pt-2">
            @auth
                <a href="{{ route(auth()->user()->homeRoute()) }}"
                   class="block w-full rounded-[14px] bg-brand-900 py-3 text-sm font-semibold text-white shadow transition hover:bg-brand-700">
                    Buka Halaman Utama ({{ ucfirst(auth()->user()->peran ?? auth()->user()->role) }})
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="block w-full rounded-[14px] bg-brand-900 py-3 text-sm font-semibold text-white shadow transition hover:bg-brand-700">
                    Masuk ke Sistem
                </a>
                <a href="{{ route('register') }}"
                   class="block w-full rounded-[14px] border border-line bg-cream-50 py-3 text-sm font-semibold text-brand-900 transition hover:bg-brand-100">
                    Daftar Sebagai Pelanggan
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
