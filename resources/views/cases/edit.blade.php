@extends('layouts.app')

@section('title', 'Edit Kasus')

@section('content')
<x-page-heading title="Edit Kasus" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('cases.update', $case) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('cases._form')
            <a href="{{ route('cases.show', $case) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
</div>
@endsection
