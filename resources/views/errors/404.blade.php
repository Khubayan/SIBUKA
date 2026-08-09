@extends('layouts.auth')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="text-center">
    <h1 class="h4 text-gray-900 mb-2">404</h1>
    <p class="text-muted mb-4">Halaman yang Anda cari tidak ditemukan.</p>
    <a href="{{ url('/') }}" class="btn btn-primary btn-user btn-block">Kembali ke Beranda</a>
</div>
@endsection
