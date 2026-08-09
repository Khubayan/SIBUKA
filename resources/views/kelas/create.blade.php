@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<x-page-heading title="Tambah Kelas" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('kelas.store') }}" method="POST">
            @csrf
            @include('kelas._form')
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
