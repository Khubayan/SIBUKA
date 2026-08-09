@extends('layouts.siswa-portal')

@section('title', 'Beranda')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Portal Siswa</h1>
</div>

<p class="mb-4">Selamat datang, <strong>{{ $student->nama }}</strong> ({{ $student->kelas?->nama ?? '-' }}).</p>

<div class="row">
    @if($student->total_poin >= 0)
        <x-stat-card title="Sisa Poin" :value="$student->total_poin" icon="star" color="success" />
    @else
        <x-stat-card title="Sisa Poin" :value="$student->total_poin" icon="star" color="warning" />
    @endif
    <x-stat-card title="Pelanggaran Terkonfirmasi" :value="$student->cases()->where('status', \App\Enums\CaseStatus::Confirmed)->where('kategori', \App\Enums\CaseCategory::Pelanggaran)->count()" icon="exclamation-triangle" color="danger" />
    <x-stat-card title="Prestasi Terkonfirmasi" :value="$student->cases()->where('status', \App\Enums\CaseStatus::Confirmed)->where('kategori', \App\Enums\CaseCategory::Prestasi)->count()" icon="award" color="primary" />
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Pelanggaran Terbaru</h6>
                <a href="{{ route('siswa.pelanggaran') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($recentPelanggaran->isEmpty())
                    <p class="text-muted mb-0">Belum ada catatan pelanggaran.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Judul</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPelanggaran as $case)
                                    <tr>
                                        <td>{{ $case->tanggal_kejadian->format('d/m/Y') }}</td>
                                        <td>{{ $case->judul }}</td>
                                        <td><span class="badge badge-danger">{{ $case->poin }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Info Sisa Poin</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">Total poin Anda saat ini:</p>
                <h3 class="font-weight-bold text-{{ $student->total_poin >= 0 ? 'success' : 'danger' }}">
                    {{ $student->total_poin > 0 ? '+' : '' }}{{ $student->total_poin }}
                </h3>
                <p class="small text-muted mb-2">Batas perhatian:</p>
                <ul class="small mb-3">
                    <li>Warning: ≤ {{ $thresholds['warning'] }}</li>
                    <li>SP1: ≤ {{ $thresholds['sp1'] }}</li>
                    <li>SP2: ≤ {{ $thresholds['sp2'] }}</li>
                    <li>SP3: ≤ {{ $thresholds['sp3'] }}</li>
                </ul>
                <a href="{{ route('siswa.poin') }}" class="btn btn-sm btn-outline-primary btn-block">Detail Poin</a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <a href="{{ route('siswa.biodata.edit') }}" class="btn btn-warning btn-block">
                    <i class="fas fa-edit"></i> Perbarui Biodata
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
