@extends('layouts.siswa-portal')

@section('title', 'Info Pelanggaran')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Info Pelanggaran</h1>
    <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pelanggaran Terkonfirmasi</h6>
    </div>
    <div class="card-body">
        @if($cases->isEmpty())
            <p class="text-muted mb-0">Belum ada catatan pelanggaran.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Jenis Kasus</th>
                            <th>Deskripsi</th>
                            <th>Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cases as $case)
                            <tr>
                                <td>{{ $case->tanggal_kejadian->format('d/m/Y') }}</td>
                                <td>{{ $case->judul }}</td>
                                <td>{{ $case->caseType?->nama ?? '-' }}</td>
                                <td>{{ Str::limit($case->deskripsi, 60) }}</td>
                                <td><span class="badge badge-danger">{{ $case->poin }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $cases->links() }}</div>
        @endif
    </div>
</div>
@endsection
