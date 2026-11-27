@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
<div class="flex min-h-screen">

    {{-- Panel merek (tablet/desktop) - Figma K1 --}}
    <aside class="hidden w-[460px] shrink-0 flex-col justify-center gap-5 bg-brand-900 p-12 text-white md:flex">
        <div class="flex size-16 items-center justify-center rounded-full bg-cream overflow-hidden border-2 border-white/25 shadow">
            <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafeteria De Barrio" class="w-full h-full object-cover">
        </div>
        <h1 class="text-4xl font-bold tracking-tight">De Barrio</h1>
        <p class="text-[15px] leading-6 text-[#D7E5DC]">
            Daftar akun pelanggan untuk menikmati kemudahan pemesanan, melihat riwayat pesanan, dan mengumpulkan poin.
        </p>
        <ul class="space-y-3.5 text-sm font-medium text-[#D7E5DC]">
            <li class="flex items-center gap-2.5">
                <span class="flex size-[22px] items-center justify-center rounded-full bg-brand-700 text-cream">
                    <x-lucide.check class="size-3.5" />
                </span>
                Simpan riwayat pesanan favorit Anda
            </li>
            <li class="flex items-center gap-2.5">
                <span class="flex size-[22px] items-center justify-center rounded-full bg-brand-700 text-cream">
                    <x-lucide.check class="size-3.5" />
                </span>
                Pesan langsung dari meja lewat scan QR
            </li>
        </ul>
    </aside>

    {{-- Form Registrasi Pelanggan --}}
    <main class="flex flex-1 items-center justify-center px-6 py-10">
        <div class="w-full max-w-[380px] space-y-5">

            <div class="flex size-[52px] items-center justify-center rounded-full bg-cream overflow-hidden border border-brand-900/20 shadow md:hidden">
                <img src="{{ asset('images/logo-de-barrio.jpg') }}" alt="Cafeteria De Barrio" class="w-full h-full object-cover">
            </div>

            <div class="space-y-2">
                <h2 class="text-[26px] font-bold text-ink">Daftar Akun</h2>
                <p class="text-sm text-muted">Daftar sebagai pelanggan Warkop Cafetaria De Barrio.</p>
            </div>

            {{-- Tab Masuk / Daftar --}}
            <div class="flex rounded-xl bg-brand-100 p-1 text-sm">
                <a href="{{ route('login') }}" class="flex-1 rounded-[9px] py-2.5 text-center font-semibold text-muted transition hover:text-ink">Masuk</a>
                <a href="{{ route('register') }}" class="flex-1 rounded-[9px] bg-white py-2.5 text-center font-bold text-brand-900 shadow-sm">Daftar</a>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label for="name" class="text-xs font-medium text-muted">Nama lengkap</label>
                    <div class="flex items-center gap-2.5 rounded-xl border bg-white px-3.5 py-3 transition {{ $errors->has('name') ? 'border-danger' : 'border-line' }} focus-within:border-brand-900">
                        <x-lucide.user class="size-[18px] text-muted" />
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                               placeholder="Nama lengkap Anda"
                               class="w-full bg-transparent text-sm text-ink outline-none placeholder:text-[#9AA59E]">
                    </div>
                    @error('name')
                        <p class="text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-medium text-muted">Email</label>
                    <div class="flex items-center gap-2.5 rounded-xl border bg-white px-3.5 py-3 transition {{ $errors->has('email') ? 'border-danger' : 'border-line' }} focus-within:border-brand-900">
                        <x-lucide.mail class="size-[18px] text-muted" />
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               placeholder="nama@email.com"
                               class="w-full bg-transparent text-sm text-ink outline-none placeholder:text-[#9AA59E]">
                    </div>
                    @error('email')
                        <p class="text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-medium text-muted">Kata sandi</label>
                    <div class="flex items-center gap-2.5 rounded-xl border bg-white px-3.5 py-3 transition {{ $errors->has('password') ? 'border-danger' : 'border-line' }} focus-within:border-brand-900">
                        <x-lucide.lock class="size-[18px] text-muted" />
                        <input id="password" name="password" type="password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full bg-transparent text-sm text-ink outline-none placeholder:text-[#9AA59E]">
                    </div>
                    @error('password')
                        <p class="text-xs font-medium text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-medium text-muted">Konfirmasi kata sandi</label>
                    <div class="flex items-center gap-2.5 rounded-xl border border-line bg-white px-3.5 py-3 focus-within:border-brand-900">
                        <x-lucide.lock class="size-[18px] text-muted" />
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               placeholder="Ulangi kata sandi"
                               class="w-full bg-transparent text-sm text-ink outline-none placeholder:text-[#9AA59E]">
                    </div>
                </div>

                <button type="submit"
                        class="w-full rounded-[14px] bg-brand-900 py-3.5 text-[15px] font-semibold text-white shadow transition hover:bg-brand-700">
                    Daftar Sekarang
                </button>
            </form>

            <a href="{{ route('landing') }}"
               class="block w-full rounded-[14px] border border-line bg-white py-3 text-center text-sm font-semibold text-brand-900 transition hover:bg-brand-100">
                Lanjut tanpa akun
            </a>
        </div>
    </main>
</div>
@endsection
