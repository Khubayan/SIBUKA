@extends('layouts.siswa-portal')

@section('title', 'Info Sisa Poin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Info Sisa Poin</h1>
    <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4 border-left-{{ $student->total_poin >= 0 ? 'success' : 'warning' }}">
            <div class="card-body text-center">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Sisa Poin Saat Ini</div>
                <div class="h2 font-weight-bold text-gray-800">
                    {{ $student->total_poin > 0 ? '+' : '' }}{{ $student->total_poin }}
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">Total prestasi: <strong class="text-success">+{{ $prestasiTotal }}</strong></p>
                <p class="mb-2">Total pelanggaran: <strong class="text-danger">{{ $pelanggaranTotal }}</strong></p>
                <hr>
                <p class="small text-muted mb-1">Batas perhatian:</p>
                <ul class="small mb-0">
                    <li>Warning: ≤ {{ $thresholds['warning'] }}</li>
                    <li>SP1: ≤ {{ $thresholds['sp1'] }}</li>
                    <li>SP2: ≤ {{ $thresholds['sp2'] }}</li>
                    <li>SP3: ≤ {{ $thresholds['sp3'] }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Perubahan Poin</h6>
            </div>
            <div class="card-body">
                @if($pointLogs->isEmpty())
                    <p class="text-muted mb-0">Belum ada riwayat perubahan poin.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Perubahan</th>
                                    <th>Sebelum</th>
                                    <th>Sesudah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pointLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $log->poin_perubahan >= 0 ? 'success' : 'danger' }}">
                                                {{ $log->poin_perubahan > 0 ? '+' : '' }}{{ $log->poin_perubahan }}
                                            </span>
                                        </td>
                                        <td>{{ $log->poin_sebelum }}</td>
                                        <td>{{ $log->poin_sesudah }}</td>
                                        <td>{{ $log->keterangan }}</td>
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
