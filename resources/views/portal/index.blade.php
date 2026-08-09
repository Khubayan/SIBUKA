@extends('layouts.portal')

@section('title', 'Portal Orang Tua')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Portal Orang Tua</h1>
    @if($unreadCount > 0)
        <a href="{{ route('portal.notifications') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-bell fa-sm"></i> {{ $unreadCount }} Notifikasi Baru
        </a>
    @endif
</div>

<p class="mb-4">Selamat datang, <strong>{{ auth()->user()->name }}</strong>.</p>

@if($children->isEmpty())
    <div class="card shadow mb-4">
        <div class="card-body text-center py-5">
            <i class="fas fa-child fa-3x text-gray-300 mb-3"></i>
            <p class="text-muted mb-0">Belum ada data anak yang terhubung dengan akun Anda.</p>
            <p class="text-muted small">Hubungi administrator sekolah untuk menghubungkan akun dengan data siswa.</p>
        </div>
    </div>
@else
    <div class="row">
        @foreach($children as $child)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ $child->kelas?->nama ?? '-' }}
                        </div>
                        <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $child->nama }}</div>
                        <div class="small text-muted mb-2">NIS: {{ $child->nis }}</div>
                        <div class="mb-3">
                            Total Poin:
                            <span class="badge badge-{{ $child->total_poin >= 0 ? 'success' : 'warning' }}">
                                {{ $child->total_poin > 0 ? '+' : '' }}{{ $child->total_poin }}
                            </span>
                        </div>
                        <a href="{{ route('portal.child', $child) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Lihat Riwayat
                        </a>
                        <a href="{{ route('portal.child.report', $child) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-download"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
