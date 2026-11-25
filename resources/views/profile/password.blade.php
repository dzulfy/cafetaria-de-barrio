@php
    $layout = match(auth()->user()->peran ?? auth()->user()->role) {
        'admin' => 'layouts.admin',
        'kasir' => 'layouts.kasir',
        default => 'layouts.pelanggan',
    };
@endphp

@extends($layout)

@section('title', 'Ubah Kata Sandi')

@section('content')
<div class="{{ (auth()->user()->peran ?? auth()->user()->role) === 'pelanggan' ? 'p-5' : 'max-w-xl' }} space-y-4">
    <div>
        <h1 class="text-[24px] font-bold text-ink">Ubah Kata Sandi</h1>
        <p class="text-xs text-muted">Amankan akun Anda dengan menggunakan kata sandi yang kuat.</p>
    </div>

    @if (session('success'))
        <p class="rounded-xl bg-ok-bg px-4 py-3 text-xs font-semibold text-brand-900 shadow-sm">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4 rounded-[14px] border border-line bg-white p-5 shadow-sm">
        @csrf
        @method('PUT')

        <div class="space-y-1.5">
            <label for="current_password" class="text-xs font-medium text-muted">Kata sandi saat ini</label>
            <div class="flex items-center gap-2.5 rounded-xl border bg-white px-3.5 py-2.5 transition {{ $errors->has('current_password') ? 'border-danger' : 'border-line' }} focus-within:border-brand-900">
                <x-lucide.lock class="size-4 text-muted" />
                <input id="current_password" name="current_password" type="password" required
                       placeholder="Masukkan kata sandi lama"
                       class="w-full bg-transparent text-xs text-ink outline-none placeholder:text-[#9AA59E]">
            </div>
            @error('current_password')
                <p class="text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="password" class="text-xs font-medium text-muted">Kata sandi baru</label>
            <div class="flex items-center gap-2.5 rounded-xl border bg-white px-3.5 py-2.5 transition {{ $errors->has('password') ? 'border-danger' : 'border-line' }} focus-within:border-brand-900">
                <x-lucide.lock class="size-4 text-muted" />
                <input id="password" name="password" type="password" required
                       placeholder="Minimal 8 karakter"
                       class="w-full bg-transparent text-xs text-ink outline-none placeholder:text-[#9AA59E]">
            </div>
            @error('password')
                <p class="text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="password_confirmation" class="text-xs font-medium text-muted">Konfirmasi kata sandi baru</label>
            <div class="flex items-center gap-2.5 rounded-xl border border-line bg-white px-3.5 py-2.5 focus-within:border-brand-900">
                <x-lucide.lock class="size-4 text-muted" />
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       placeholder="Ulangi kata sandi baru"
                       class="w-full bg-transparent text-xs text-ink outline-none placeholder:text-[#9AA59E]">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full rounded-[10px] bg-brand-900 py-3 text-xs font-semibold text-white shadow transition hover:bg-brand-700">
                Simpan Kata Sandi Baru
            </button>
        </div>
    </form>
</div>
@endsection
