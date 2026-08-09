@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Profil Siswa</h1>
    <div>
        @if($canManage)
            <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm"></i> Edit
            </a>
            <a href="{{ route('cases.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm"></i> Tambah Kasus
            </a>
            <a href="{{ route('reports.student', ['student' => $student, 'format' => 'pdf']) }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-file-pdf fa-sm"></i> Cetak Laporan
            </a>
            <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus siswa ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger shadow-sm"><i class="fas fa-trash fa-sm"></i> Hapus</button>
            </form>
        @endif
        <a href="{{ route('students.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                @if($student->foto)
                    <img src="{{ asset('storage/' . $student->foto) }}" class="img-fluid rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover" alt="">
                @else
                    <img src="{{ asset('assets/sb-admin-2/img/undraw_profile.svg') }}" class="mb-3" style="width:120px" alt="">
                @endif
                <h4 class="font-weight-bold text-gray-800">{{ $student->nama }}</h4>
                <p class="text-muted mb-1">NIS: {{ $student->nis }}</p>
                @if($student->nisn)<p class="text-muted mb-1">NISN: {{ $student->nisn }}</p>@endif
                <p class="mb-2">{{ $student->kelas?->nama }} — {{ $student->kelas?->jurusan?->nama }}</p>
                <h5>
                    Total Poin:
                    <span class="badge badge-{{ $student->total_poin >= 0 ? 'success' : 'warning' }} badge-lg">
                        {{ $student->total_poin > 0 ? '+' : '' }}{{ $student->total_poin }}
                    </span>
                </h5>
                <span class="badge badge-{{ $student->status->value === 'aktif' ? 'primary' : 'secondary' }}">{{ ucfirst($student->status->value) }}</span>
            </div>
            <div class="card-footer">
                <small class="text-muted">
                    {{ $student->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }},
                    lahir {{ $student->tanggal_lahir->format('d/m/Y') }}<br>
                    @if($student->alamat){{ $student->alamat }}<br>@endif
                    @if($student->no_hp)HP: {{ $student->no_hp }}@endif
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pelanggaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pelanggaran'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Prestasi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['prestasi'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Kasus</h6>
                @if($canManage)
                    <a href="{{ route('cases.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus fa-sm"></i> Tambah Kasus
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($cases->isEmpty())
                    <p class="text-muted mb-0">Belum ada riwayat kasus.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Judul</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cases as $case)
                                    <tr>
                                        <td>{{ $case->tanggal_kejadian->format('d/m/Y') }}</td>
                                        <td><x-badge-kategori :kategori="$case->kategori" /></td>
                                        <td>
                                            @if($canManage)
                                                <a href="{{ route('cases.show', $case) }}">{{ $case->judul }}</a>
                                            @else
                                                {{ $case->judul }}
                                            @endif
                                        </td>
                                        <td>{{ $case->poin > 0 ? '+' : '' }}{{ $case->poin }}</td>
                                        <td>{{ ucfirst($case->status->value) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
