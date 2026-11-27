@extends('layouts.pelanggan')

@section('title', 'Akun')

@section('content')
@php
    $user = auth()->user();
    $displayName = $user->nama ?? $user->name ?? 'Pelanggan';
    $initials = strtoupper(collect(explode(' ', trim($displayName)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') ?: 'PL');
@endphp

<div class="px-5 pt-5 pb-6 space-y-4">
    <h1 class="text-[22px] font-bold text-ink tracking-tight">Akun</h1>

    {{-- Profile Card (Figma 3:190) --}}
    <div class="flex items-center gap-3.5 rounded-2xl border border-line bg-white p-4 shadow-2xs">
        <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-brand-900 text-base font-bold text-cream">
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

    {{-- Account Menu List (Figma 3:213) --}}
    <div class="rounded-2xl border border-line bg-white px-4 divide-y divide-line shadow-2xs">
        <a href="{{ route('orders.history', ['table' => session('active_table', '05')]) }}"
           class="flex items-center justify-between py-3.5 text-xs font-semibold text-ink transition hover:text-brand-900">
            <div class="flex items-center gap-3">
                <svg class="size-5 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8M8 15h5" />
                </svg>
                <span>Riwayat Pesanan</span>
            </div>
            <svg class="size-4 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
            </svg>
        </a>

        <a href="{{ route('password.edit') }}"
           class="flex items-center justify-between py-3.5 text-xs font-semibold text-ink transition hover:text-brand-900">
            <div class="flex items-center gap-3">
                <x-lucide.lock class="size-5 text-muted" />
                <span>Ubah Kata Sandi</span>
            </div>
            <svg class="size-4 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
            </svg>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-between py-3.5 text-xs font-semibold text-danger transition hover:opacity-80 cursor-pointer">
                <div class="flex items-center gap-3">
                    <x-lucide.log-out class="size-5 text-danger" />
                    <span>Keluar</span>
                </div>
                <svg class="size-4 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                </svg>
            </button>
        </form>
    </div>
</div>
@endsection
