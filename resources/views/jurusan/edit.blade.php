@extends('layouts.app')

@section('title', 'Edit Jurusan')

@section('content')
<x-page-heading title="Edit Jurusan" />

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Jurusan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('jurusan.update', $jurusan) }}" method="POST">
            @csrf
            @method('PUT')
            @include('jurusan._form')
            <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
</div>
@endsection
