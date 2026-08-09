@extends('layouts.app')

@section('title', 'Tambah Kasus')

@section('content')
<x-page-heading title="Tambah Kasus" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('cases.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('cases._form')
            <a href="{{ route('cases.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
