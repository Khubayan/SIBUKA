@extends('layouts.app')

@section('title', 'Manajemen Kasus')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Kasus</h1>
    @if($canManage)
        <a href="{{ route('cases.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Kasus
        </a>
    @endif
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form method="GET" class="form-row align-items-end">
            <div class="col-md-2 mb-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari judul / siswa..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 mb-2">
                <select name="student_id" class="form-control form-control-sm">
                    <option value="">Semua Siswa</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" @selected(request('student_id') == $s->id)>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="kelas_id" class="form-control form-control-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="kategori" class="form-control form-control-sm">
                    <option value="">Semua Kategori</option>
                    @foreach(\App\Enums\CaseCategory::cases() as $cat)
                        <option value="{{ $cat->value }}" @selected(request('kategori') === $cat->value)>{{ ucfirst($cat->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\CaseStatus::cases() as $st)
                        <option value="{{ $st->value }}" @selected(request('status') === $st->value)>{{ ucfirst($st->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 mb-2">
                <input type="date" name="tanggal_from" class="form-control form-control-sm" value="{{ request('tanggal_from') }}" title="Dari tanggal">
            </div>
            <div class="col-md-1 mb-2">
                <input type="date" name="tanggal_to" class="form-control form-control-sm" value="{{ request('tanggal_to') }}" title="Sampai tanggal">
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
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Poin</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cases as $case)
                        <tr>
                            <td>{{ $case->tanggal_kejadian->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('students.show', $case->student) }}">{{ $case->student?->nama ?? '-' }}</a>
                            </td>
                            <td>{{ $case->student?->kelas?->nama ?? '-' }}</td>
                            <td>
                                <a href="{{ route('cases.show', $case) }}">{{ $case->judul }}</a>
                            </td>
                            <td><x-badge-kategori :kategori="$case->kategori" /></td>
                            <td>
                                <span class="badge badge-{{ $case->poin >= 0 ? 'success' : 'danger' }}">
                                    {{ $case->poin > 0 ? '+' : '' }}{{ $case->poin }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $case->status->value === 'confirmed' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($case->status->value) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('cases.show', $case) }}" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                                @if($canManage)
                                    <a href="{{ route('cases.edit', $case) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data kasus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $cases->links() }}</div>
    </div>
</div>
@endsection
