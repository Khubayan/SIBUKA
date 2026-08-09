@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Siswa</h1>
    @if($canManage)
        <div>
            <a href="{{ route('students.template') }}" class="btn btn-sm btn-secondary shadow-sm mr-1">
                <i class="fas fa-download fa-sm"></i> Template CSV
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm"></i> Tambah Siswa
            </a>
        </div>
    @endif
</div>

@if($canManage)
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Import CSV</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="form-inline">
            @csrf
            <input type="file" name="file" class="form-control form-control-sm mr-2" accept=".csv,.txt" required>
            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-file-import"></i> Import</button>
        </form>
        <small class="text-muted d-block mt-2">Format: nis, nisn, nama, jenis_kelamin, tanggal_lahir, kelas, alamat, no_hp</small>
    </div>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form method="GET" class="form-row align-items-end">
            <div class="col-md-3 mb-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIS / nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 mb-2">
                <select name="kelas_id" class="form-control form-control-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }} ({{ $k->jurusan?->kode }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="jurusan_id" class="form-control form-control-sm">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j->id }}" @selected(request('jurusan_id') == $j->id)>{{ $j->kode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\StudentStatus::cases() as $st)
                        <option value="{{ $st->value }}" @selected(request('status') === $st->value)>{{ ucfirst($st->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>JK</th>
                        <th>Total Poin</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->nis }}</td>
                            <td>
                                <a href="{{ route('students.show', $student) }}">{{ $student->nama }}</a>
                            </td>
                            <td>{{ $student->kelas?->nama ?? '-' }}</td>
                            <td>{{ $student->jenis_kelamin }}</td>
                            <td>
                                <span class="badge badge-{{ $student->total_poin >= 0 ? 'success' : 'warning' }}">
                                    {{ $student->total_poin > 0 ? '+' : '' }}{{ $student->total_poin }}
                                </span>
                            </td>
                            <td><span class="badge badge-{{ $student->status->value === 'aktif' ? 'primary' : 'secondary' }}">{{ ucfirst($student->status->value) }}</span></td>
                            <td>
                                <a href="{{ route('students.show', $student) }}" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                                @if($canManage)
                                    <a href="{{ route('students.edit', $student) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $students->links() }}</div>
    </div>
</div>
@endsection
