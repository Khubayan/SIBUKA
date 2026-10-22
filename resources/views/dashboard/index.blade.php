@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    @if(auth()->user()->isAdmin() || auth()->user()->isGuruBk() || auth()->user()->isWaliKelas())
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-file-alt fa-sm"></i> Laporan
        </a>
    @endif
</div>

<p class="mb-4">
    Selamat datang, <strong>{{ auth()->user()->name }}</strong>.
    @if($isWaliKelas)
        <span class="text-muted">(Data ditampilkan untuk kelas yang Anda ampu)</span>
    @endif
</p>

<div class="row">
    <x-stat-card title="Total Siswa" :value="$stats['total_siswa']" icon="user-graduate" color="primary" />
    <x-stat-card title="Pelanggaran Bulan Ini" :value="$stats['pelanggaran_bulan_ini']" icon="exclamation-triangle" color="danger" />
    <x-stat-card title="Prestasi Bulan Ini" :value="$stats['prestasi_bulan_ini']" icon="award" color="success" />
    <x-stat-card title="Perlu Perhatian" :value="$stats['perlu_perhatian']" icon="exclamation-triangle" color="warning" />
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Kasus Terbaru</h6>
            </div>
            <div class="card-body">
                @if($kasusTerbaru->isEmpty())
                    <p class="text-muted mb-0">Belum ada data kasus.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kasusTerbaru as $kasus)
                                    <tr>
                                        <td>{{ $kasus->tanggal_kejadian->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('students.show', $kasus->student) }}">{{ $kasus->student?->nama ?? '-' }}</a>
                                        </td>
                                        <td>
                                            @if(auth()->user()->isAdmin() || auth()->user()->isGuruBk())
                                                <a href="{{ route('cases.show', $kasus) }}">{{ $kasus->judul }}</a>
                                            @else
                                                {{ $kasus->judul }}
                                            @endif
                                        </td>
                                        <td><x-badge-kategori :kategori="$kasus->kategori" /></td>
                                        <td>
                                            <span class="badge badge-{{ $kasus->poin >= 0 ? 'success' : 'danger' }}">
                                                {{ $kasus->poin > 0 ? '+' : '' }}{{ $kasus->poin }}
                                            </span>
                                        </td>
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
                <h6 class="m-0 font-weight-bold text-primary">Tren Kasus {{ now()->year }}</h6>
            </div>
            <div class="card-body">
                <canvas id="trenChart" height="200"></canvas>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi</h6>
            </div>
            <div class="card-body">
                <p class="small text-gray-800 mb-2">
                    <i class="fas fa-info-circle text-primary mr-1"></i>
                    Siswa dengan poin ≤ {{ config('sibuka.point_thresholds.warning') }} memerlukan perhatian khusus.
                </p>
                <p class="small text-muted mb-0">
                    Role: <strong>{{ auth()->user()->role->label() }}</strong>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/sb-admin-2/vendor/chart.js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('trenChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($trenBulanan['labels']),
            datasets: [
                {
                    label: 'Pelanggaran',
                    data: @json($trenBulanan['pelanggaran']),
                    backgroundColor: '#e74a3b',
                },
                {
                    label: 'Prestasi',
                    data: @json($trenBulanan['prestasi']),
                    backgroundColor: '#1cc88a',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{ stacked: false }],
                yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
            },
            legend: { display: true, position: 'bottom' }
        }
    });
});
</script>
@endpush
