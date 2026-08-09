@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<form class="user" method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <input type="email" name="email"
               class="form-control form-control-user @error('email') is-invalid @enderror"
               placeholder="Alamat Email..." value="{{ old('email') }}" required autofocus>
    </div>
    <div class="form-group">
        <input type="password" name="password"
               class="form-control form-control-user @error('password') is-invalid @enderror"
               placeholder="Password" required>
    </div>
    <div class="form-group">
        <div class="custom-control custom-checkbox small">
            <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="custom-control-label" for="remember">Ingat Saya</label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary btn-user btn-block">Masuk</button>
</form>
@endsection
