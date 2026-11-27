@extends('layouts.pelanggan')

@section('title', 'Akun')

@section('content')
@php
    $user = auth()->user();
    $displayName = $user->nama ?? $user->name ?? 'Pelanggan';
    $initials = strtoupper(collect(explode(' ', trim($displayName)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') ?: 'PL');
@endphp

<div class="px-5 md:px-6 pt-5 md:pt-8 pb-6 max-w-3xl mx-auto space-y-5">
    <h1 class="text-[22px] sm:text-2xl font-bold text-ink tracking-tight animate-fade-up">Akun</h1>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6 items-start">
        {{-- Profile Card --}}
        <div class="md:col-span-5 flex items-center gap-3.5 rounded-2xl border border-line bg-white p-4 sm:p-5 shadow-2xs animate-fade-up">
            <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-brand-900 text-base font-bold text-cream shadow-xs">
                {{ $initials }}
            </div>
            <div class="min-w-0">
                <h2 class="truncate text-sm font-bold text-ink">{{ $displayName }}</h2>
                <p class="truncate text-xs text-muted mt-0.5">{{ $user->email }}</p>
                <span class="mt-1.5 inline-block rounded-full bg-brand-100 px-2.5 py-0.5 text-[11px] font-semibold text-brand-900">
                    Pelanggan
                </span>
            </div>
        </div>

        {{-- Account Menu List --}}
        <div class="md:col-span-7 rounded-2xl border border-line bg-white px-4 divide-y divide-line shadow-2xs animate-fade-up" style="animation-delay: 80ms;">
            <a href="{{ route('orders.history', session('active_table') ? ['table' => session('active_table')] : []) }}"
               class="group flex items-center justify-between py-3.5 text-xs sm:text-[13px] font-semibold text-ink transition hover:text-brand-900">
                <div class="flex items-center gap-3">
                    <svg class="size-5 text-muted transition group-hover:text-brand-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8M8 15h5" />
                    </svg>
                    <span>Riwayat Pesanan</span>
                </div>
                <svg class="size-4 text-muted transition-transform duration-200 group-hover:translate-x-1 group-hover:text-brand-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                </svg>
            </a>

            <a href="{{ route('password.edit') }}"
               class="group flex items-center justify-between py-3.5 text-xs sm:text-[13px] font-semibold text-ink transition hover:text-brand-900">
                <div class="flex items-center gap-3">
                    <x-lucide.lock class="size-5 text-muted transition group-hover:text-brand-900" />
                    <span>Ubah Kata Sandi</span>
                </div>
                <svg class="size-4 text-muted transition-transform duration-200 group-hover:translate-x-1 group-hover:text-brand-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                </svg>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="group flex w-full items-center justify-between py-3.5 text-xs sm:text-[13px] font-semibold text-danger transition hover:opacity-80 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <x-lucide.log-out class="size-5 text-danger" />
                        <span>Keluar</span>
                    </div>
                    <svg class="size-4 text-muted transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
