<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form method="GET" class="form-inline">
            <div class="input-group input-group-sm mr-2">
                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
            @if(request()->hasAny(['search', 'jurusan_id', 'kategori', 'role']))
                <a href="{{ url()->current() }}" class="btn btn-sm btn-secondary">Reset</a>
            @endif
            {{ $slot ?? '' }}
        </form>
    </div>
</div>
