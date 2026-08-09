@props(['title', 'createRoute' => null, 'createLabel' => 'Tambah'])

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    @if($createRoute)
        <a href="{{ $createRoute }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ $createLabel }}
        </a>
    @endif
</div>
