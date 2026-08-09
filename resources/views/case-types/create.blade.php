@extends('layouts.app')

@section('title', 'Tambah Jenis Kasus')

@section('content')
<x-page-heading title="Tambah Jenis Kasus" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('case-types.store') }}" method="POST">
            @csrf
            @include('case-types._form')
            <a href="{{ route('case-types.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
