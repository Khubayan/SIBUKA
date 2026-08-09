@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<x-page-heading title="Edit Kelas" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('kelas.update', $kelas) }}" method="POST">
            @csrf
            @method('PUT')
            @include('kelas._form')
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
</div>
@endsection
