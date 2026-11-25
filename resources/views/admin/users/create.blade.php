@extends('layouts.admin')
@section('title', 'Tambah Pengguna')

@section('content')
    <div class="space-y-1">
        <h1 class="text-[26px] font-bold text-ink">Tambah Pengguna Baru</h1>
        <p class="text-[13px] text-muted">Tambahkan akun Admin, Kasir, atau Pelanggan ke sistem.</p>
    </div>

    @include('admin.users._form')
@endsection
