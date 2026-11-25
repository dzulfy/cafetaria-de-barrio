@extends('layouts.guest')

@section('title', 'Akses Ditolak (403)')

@section('content')
<div class="flex min-h-screen flex-col items-center justify-center p-6 text-center">
    <div class="w-full max-w-sm space-y-4 rounded-2xl border border-line bg-white p-8 shadow-sm">
        <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-danger-bg text-danger shadow-inner">
            <x-lucide.lock class="size-8" />
        </span>

        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-ink">403 - Akses Ditolak</h1>
            <p class="text-xs text-muted leading-relaxed">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka halaman ini.' }}
            </p>
        </div>

        <div class="pt-3">
            @auth
                <a href="{{ route(auth()->user()->homeRoute()) }}"
                   class="inline-block w-full rounded-[12px] bg-brand-900 py-2.5 text-xs font-semibold text-white shadow transition hover:bg-brand-700">
                    Kembali ke Halaman Saya
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-block w-full rounded-[12px] bg-brand-900 py-2.5 text-xs font-semibold text-white shadow transition hover:bg-brand-700">
                    Masuk ke Sistem
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
