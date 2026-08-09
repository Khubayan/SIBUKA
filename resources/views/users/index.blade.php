@extends('layouts.app')

@section('title', 'Data Pengguna')

@section('content')
<x-page-heading title="Data Pengguna" :createRoute="route('users.create')" createLabel="Tambah Pengguna" />

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna</h6>
        <form method="GET" class="form-inline mt-2 mt-md-0">
            <select name="role" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Semua Role</option>
                @foreach(\App\Enums\UserRole::cases() as $role)
                    <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $item)
                        <tr class="{{ !$item->is_active ? 'table-secondary' : '' }}">
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td><span class="badge badge-info">{{ $item->role->label() }}</span></td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $item) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                @if($item->is_active && $item->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Nonaktifkan pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Nonaktifkan"><i class="fas fa-user-slash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $users->links() }}</div>
    </div>
</div>
@endsection
