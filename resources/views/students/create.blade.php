@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<x-page-heading title="Tambah Siswa" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('students._form')
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
