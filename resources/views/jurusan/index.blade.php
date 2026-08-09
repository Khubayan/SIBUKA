@extends('layouts.app')

@section('title', 'Data Jurusan')

@section('content')
<x-page-heading title="Data Jurusan" :createRoute="route('jurusan.create')" createLabel="Tambah Jurusan" />

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Jurusan</h6>
        <form method="GET" class="form-inline">
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
                        <th>Kode</th>
                        <th>Nama Jurusan</th>
                        <th>Jumlah Kelas</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurusan as $index => $item)
                        <tr>
                            <td>{{ $jurusan->firstItem() + $index }}</td>
                            <td><strong>{{ $item->kode }}</strong></td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kelas_count }}</td>
                            <td>
                                <a href="{{ route('jurusan.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('jurusan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jurusan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data jurusan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $jurusan->links() }}</div>
    </div>
</div>
@endsection
