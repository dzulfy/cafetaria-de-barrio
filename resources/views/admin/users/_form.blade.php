@php $isEdit = isset($user); @endphp

<form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}"
      class="max-w-xl space-y-5 rounded-[14px] border border-line bg-white p-6 shadow-sm">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="space-y-1.5">
        <label for="name" class="text-xs font-medium text-muted">Nama lengkap</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->nama ?? $user->name ?? '') }}" required
               placeholder="Masukkan nama lengkap"
               class="w-full rounded-xl border px-3.5 py-3 text-sm outline-none transition focus:border-brand-900 {{ $errors->has('name') ? 'border-danger' : 'border-line' }}">
        @error('name') <p class="text-xs font-medium text-danger">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-1.5">
        <label for="email" class="text-xs font-medium text-muted">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
               placeholder="nama@email.com"
               class="w-full rounded-xl border px-3.5 py-3 text-sm outline-none transition focus:border-brand-900 {{ $errors->has('email') ? 'border-danger' : 'border-line' }}">
        @error('email') <p class="text-xs font-medium text-danger">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-1.5">
        <label for="role" class="text-xs font-medium text-muted">Peran</label>
        <select id="role" name="role" required class="w-full rounded-xl border border-line bg-white px-3.5 py-3 text-sm outline-none focus:border-brand-900">
            @foreach (['admin' => 'Admin / Owner', 'kasir' => 'Kasir', 'pelanggan' => 'Pelanggan'] as $v => $l)
                <option value="{{ $v }}" @selected(old('role', $user->peran ?? $user->role ?? 'kasir') === $v)>{{ $l }}</option>
            @endforeach
        </select>
        @error('role') <p class="text-xs font-medium text-danger">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="space-y-1.5">
            <label for="password" class="text-xs font-medium text-muted">Kata sandi {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label>
            <input type="password" id="password" name="password" {{ $isEdit ? '' : 'required' }}
                   placeholder="{{ $isEdit ? 'Biarkan kosong' : 'Minimal 8 karakter' }}"
                   class="w-full rounded-xl border px-3.5 py-3 text-sm outline-none transition focus:border-brand-900 {{ $errors->has('password') ? 'border-danger' : 'border-line' }}">
            @error('password') <p class="text-xs font-medium text-danger">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-1.5">
            <label for="password_confirmation" class="text-xs font-medium text-muted">Konfirmasi kata sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="Ulangi kata sandi"
                   class="w-full rounded-xl border border-line px-3.5 py-3 text-sm outline-none transition focus:border-brand-900">
        </div>
    </div>

    <label class="flex cursor-pointer items-center gap-2.5 pt-1 text-sm text-ink">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="size-4 rounded border-line accent-brand-900"
               @checked(old('is_active', $user->status_aktif ?? $user->is_active ?? true))>
        <span>Akun aktif (dapat masuk ke sistem)</span>
    </label>

    <div class="flex items-center gap-3 pt-3">
        <a href="{{ route('admin.users.index') }}" class="rounded-[10px] border border-line bg-white px-4 py-2.5 text-[13px] font-semibold text-ink transition hover:bg-cream-50">
            Batal
        </a>
        <button type="submit" class="rounded-[10px] bg-brand-900 px-5 py-2.5 text-[13px] font-semibold text-white shadow transition hover:bg-brand-700">
            {{ $isEdit ? 'Perbarui Pengguna' : 'Simpan Pengguna' }}
        </button>
    </div>
</form>
