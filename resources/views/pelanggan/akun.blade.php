@extends('layouts.pelanggan')
@section('title', 'Akun Saya')

@section('content')
<div class="p-5 space-y-5">
    <div>
        <h1 class="text-[22px] font-bold text-ink">Akun Saya</h1>
        <p class="text-xs text-muted">Informasi dan keamanan profil pelanggan.</p>
    </div>

    {{-- Profil Card - Figma 07 Akun --}}
    <div class="rounded-2xl border border-line bg-white p-5 shadow-sm space-y-4">
        <div class="flex items-center gap-3.5">
            <span class="flex size-14 items-center justify-center rounded-full bg-brand-100 text-lg font-bold text-brand-900">
                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 2)) }}
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="text-base font-bold text-ink truncate">{{ auth()->user()->nama ?? auth()->user()->name }}</h2>
                <p class="text-xs text-muted truncate">{{ auth()->user()->email }}</p>
                <span class="mt-1 inline-block rounded-full bg-brand-100 px-2 py-0.5 text-[10px] font-bold text-brand-900">
                    Pelanggan Terdaftar
                </span>
            </div>
        </div>

        <div class="border-t border-line pt-4 space-y-2.5">
            <a href="{{ route('password.edit') }}" class="flex items-center justify-between rounded-xl bg-cream-50 p-3 text-xs font-semibold text-ink transition hover:bg-brand-100 hover:text-brand-900">
                <div class="flex items-center gap-2.5">
                    <x-lucide.lock class="size-4 text-muted" />
                    <span>Ubah Kata Sandi</span>
                </div>
                <span>&rarr;</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-between rounded-xl bg-danger-bg p-3 text-xs font-semibold text-danger transition hover:bg-danger hover:text-white">
                    <div class="flex items-center gap-2.5">
                        <x-lucide.log-out class="size-4" />
                        <span>Keluar dari Akun</span>
                    </div>
                    <span>&rarr;</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
