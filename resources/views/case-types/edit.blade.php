@extends('layouts.app')

@section('title', 'Edit Jenis Kasus')

@section('content')
<x-page-heading title="Edit Jenis Kasus" />

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('case-types.update', $caseType) }}" method="POST">
            @csrf
            @method('PUT')
            @include('case-types._form')
            <a href="{{ route('case-types.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
</div>
@endsection
