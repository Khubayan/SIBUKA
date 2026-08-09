@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<x-page-heading title="Tambah Pengguna" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @include('users._form')
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleRoleFields() {
        const role = document.getElementById('role').value;
        document.getElementById('wali-kelas-fields').style.display = role === 'wali_kelas' ? 'block' : 'none';
        document.getElementById('orang-tua-fields').style.display = role === 'orang_tua' ? 'block' : 'none';
        document.getElementById('siswa-fields').style.display = role === 'siswa' ? 'block' : 'none';
    }
    document.getElementById('role').addEventListener('change', toggleRoleFields);
    toggleRoleFields();
</script>
@endpush
@endsection
