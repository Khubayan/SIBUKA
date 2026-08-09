@extends('layouts.portal')

@section('title', 'Riwayat Kasus — '.$student->nama)

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Kasus</h1>
    <div>
        <a href="{{ route('portal.child.report', $student) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-download fa-sm"></i> Download PDF
        </a>
        <a href="{{ route('portal.index') }}" class="btn btn-sm btn-light shadow-sm">Kembali</a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4 class="font-weight-bold text-gray-800">{{ $student->nama }}</h4>
                <p class="text-muted mb-1">NIS: {{ $student->nis }} · {{ $student->kelas?->nama }}</p>
            </div>
            <div class="col-md-4 text-md-right">
                <span class="text-muted">Total Poin:</span>
                <span class="badge badge-{{ $student->total_poin >= 0 ? 'success' : 'warning' }} badge-lg">
                    {{ $student->total_poin > 0 ? '+' : '' }}{{ $student->total_poin }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Kasus Terkonfirmasi</h6>
    </div>
    <div class="card-body">
        @if($cases->isEmpty())
            <p class="text-muted mb-0">Belum ada riwayat kasus untuk anak Anda.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Judul</th>
                            <th>Jenis</th>
                            <th>Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cases as $case)
                            <tr>
                                <td>{{ $case->tanggal_kejadian->format('d/m/Y') }}</td>
                                <td><x-badge-kategori :kategori="$case->kategori" /></td>
                                <td>{{ $case->judul }}</td>
                                <td>{{ $case->caseType?->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $case->poin >= 0 ? 'success' : 'danger' }}">
                                        {{ $case->poin > 0 ? '+' : '' }}{{ $case->poin }}
                                    </span>
                                </td>
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
