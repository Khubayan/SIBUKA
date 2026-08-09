@extends('layouts.app')

@section('title', 'Tambah Jurusan')

@section('content')
<x-page-heading title="Tambah Jurusan" />

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Jurusan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('jurusan.store') }}" method="POST">
            @csrf
            @include('jurusan._form')
            <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
