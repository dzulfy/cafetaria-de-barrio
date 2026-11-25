@extends('layouts.admin')
@section('title', 'Ubah Pengguna')

@section('content')
    <div class="space-y-1">
        <h1 class="text-[26px] font-bold text-ink">Ubah Data Pengguna</h1>
        <p class="text-[13px] text-muted">Perbarui informasi akun, peran, atau status pengguna.</p>
    </div>

    @include('admin.users._form')
@endsection
