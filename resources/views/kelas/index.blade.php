@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<x-page-heading title="Data Kelas" :createRoute="route('kelas.create')" createLabel="Tambah Kelas" />

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Kelas</h6>
        <form method="GET" class="form-inline mt-2 mt-md-0">
            <select name="jurusan_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Semua Jurusan</option>
                @foreach($jurusanList as $j)
                    <option value="{{ $j->id }}" @selected(request('jurusan_id') == $j->id)>{{ $j->kode }} — {{ $j->nama }}</option>
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
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th>Tingkat</th>
                        <th>Wali Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Siswa</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $index => $item)
                        <tr>
                            <td>{{ $kelas->firstItem() + $index }}</td>
                            <td><strong>{{ $item->nama }}</strong></td>
                            <td>{{ $item->jurusan?->kode }}</td>
                            <td>{{ $item->tingkat }}</td>
                            <td>{{ $item->waliKelas?->name ?? '-' }}</td>
                            <td>{{ $item->tahun_ajaran }}</td>
                            <td>{{ $item->students_count }}</td>
                            <td>
                                <a href="{{ route('kelas.edit', $item) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('kelas.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $kelas->links() }}</div>
    </div>
</div>
@endsection
