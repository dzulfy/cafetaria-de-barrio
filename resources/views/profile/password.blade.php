@php
    $role = auth()->user()->peran ?? auth()->user()->role;
    $layout = match($role) {
        'admin' => 'layouts.admin',
        'kasir' => 'layouts.kasir',
        default => 'layouts.pelanggan',
    };
@endphp

@extends($layout)

@section('title', 'Ubah Kata Sandi')

@section('content')
<div class="{{ $role === 'pelanggan' ? 'max-w-xl mx-auto px-5 md:px-6 py-6 md:py-8' : 'max-w-xl' }} space-y-4 animate-fade-up">
    <div>
        <h1 class="text-[22px] sm:text-2xl font-bold text-ink">Ubah Kata Sandi</h1>
        <p class="text-xs text-muted mt-1">Amankan akun Anda dengan menggunakan kata sandi yang kuat.</p>
    </div>

    @if (session('success'))
        <p class="rounded-xl bg-ok-bg px-4 py-3 text-xs font-semibold text-brand-900 shadow-sm">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4 rounded-2xl border border-line bg-white p-5 sm:p-6 shadow-2xs">
        @csrf
        @method('PUT')

        <div class="space-y-1.5">
            <label for="current_password" class="text-xs font-medium text-muted">Kata sandi saat ini</label>
            <div class="flex items-center gap-2.5 rounded-xl border bg-white px-3.5 py-2.5 transition {{ $errors->has('current_password') ? 'border-danger' : 'border-line' }} focus-within:border-brand-900 focus-within:ring-2 focus-within:ring-brand-900/15">
                <x-lucide.lock class="size-4 text-muted shrink-0" />
                <input id="current_password" name="current_password" type="password" required
                       placeholder="Masukkan kata sandi lama"
                       class="w-full bg-transparent text-xs sm:text-sm text-ink outline-none placeholder:text-[#9AA59E]">
                <button type="button" onclick="togglePwdVisibility('current_password', this)" class="text-xs font-semibold text-muted hover:text-ink shrink-0 cursor-pointer">
                    Lihat
                </button>
            </div>
            @error('current_password')
                <p class="text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="password" class="text-xs font-medium text-muted">Kata sandi baru</label>
            <div class="flex items-center gap-2.5 rounded-xl border bg-white px-3.5 py-2.5 transition {{ $errors->has('password') ? 'border-danger' : 'border-line' }} focus-within:border-brand-900 focus-within:ring-2 focus-within:ring-brand-900/15">
                <x-lucide.lock class="size-4 text-muted shrink-0" />
                <input id="password" name="password" type="password" required
                       placeholder="Minimal 8 karakter"
                       class="w-full bg-transparent text-xs sm:text-sm text-ink outline-none placeholder:text-[#9AA59E]">
                <button type="button" onclick="togglePwdVisibility('password', this)" class="text-xs font-semibold text-muted hover:text-ink shrink-0 cursor-pointer">
                    Lihat
                </button>
            </div>
            @error('password')
                <p class="text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="password_confirmation" class="text-xs font-medium text-muted">Konfirmasi kata sandi baru</label>
            <div class="flex items-center gap-2.5 rounded-xl border border-line bg-white px-3.5 py-2.5 transition focus-within:border-brand-900 focus-within:ring-2 focus-within:ring-brand-900/15">
                <x-lucide.lock class="size-4 text-muted shrink-0" />
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       placeholder="Ulangi kata sandi baru"
                       class="w-full bg-transparent text-xs sm:text-sm text-ink outline-none placeholder:text-[#9AA59E]">
                <button type="button" onclick="togglePwdVisibility('password_confirmation', this)" class="text-xs font-semibold text-muted hover:text-ink shrink-0 cursor-pointer">
                    Lihat
                </button>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full rounded-xl bg-brand-900 py-3 text-xs sm:text-sm font-semibold text-white shadow transition-all hover:bg-brand-700 hover:-translate-y-0.5 active:scale-[0.98] cursor-pointer">
                Simpan Kata Sandi Baru
            </button>
        </div>
    </form>
</div>

<script>
    function togglePwdVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        btn.textContent = isPwd ? 'Tutup' : 'Lihat';
    }
</script>
@endsection
