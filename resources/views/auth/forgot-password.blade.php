@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div class="text-center">
    <p class="text-muted mb-4">
        Reset password tidak tersedia secara publik. Hubungi administrator sekolah untuk bantuan akun.
    </p>
    <a href="{{ route('login') }}" class="btn btn-primary btn-user btn-block">Kembali ke Login</a>
</div>
@endsection
