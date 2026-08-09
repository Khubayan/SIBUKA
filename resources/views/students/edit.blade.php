@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<x-page-heading title="Edit Siswa" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('students._form')
            <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
</div>
@endsection
