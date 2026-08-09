@extends('layouts.app')

@section('title', 'Detail Kasus')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Kasus</h1>
    <div>
        @if($canManage)
            @if($case->status->value === 'draft' && auth()->user()->can('confirm', $case))
                <form action="{{ route('cases.confirm', $case) }}" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi kasus ini? Poin siswa akan diperbarui.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-success shadow-sm">
                        <i class="fas fa-check fa-sm"></i> Konfirmasi
                    </button>
                </form>
            @endif
            <a href="{{ route('cases.edit', $case) }}" class="btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm"></i> Edit
            </a>
            <form action="{{ route('cases.destroy', $case) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kasus ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger shadow-sm"><i class="fas fa-trash fa-sm"></i> Hapus</button>
            </form>
        @endif
        <a href="{{ route('cases.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ $case->judul }}</h6>
                <span class="badge badge-{{ $case->status->value === 'confirmed' ? 'success' : 'secondary' }}">
                    {{ ucfirst($case->status->value) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Siswa:</strong>
                        <a href="{{ route('students.show', $case->student) }}">{{ $case->student->nama }}</a>
                        <br>
                        <small class="text-muted">{{ $case->student->kelas?->nama }} — NIS {{ $case->student->nis }}</small>
                    </div>
                    <div class="col-md-6">
                        <strong>Jenis Kasus:</strong> {{ $case->caseType?->kode }} — {{ $case->caseType?->nama }}<br>
                        <strong>Kategori:</strong> <x-badge-kategori :kategori="$case->kategori" />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Tanggal Kejadian:</strong><br>
                        {{ $case->tanggal_kejadian->format('d F Y') }}
                    </div>
                    <div class="col-md-4">
                        <strong>Poin:</strong><br>
                        <span class="badge badge-{{ $case->poin >= 0 ? 'success' : 'danger' }} badge-lg">
                            {{ $case->poin > 0 ? '+' : '' }}{{ $case->poin }}
                        </span>
                    </div>
                    <div class="col-md-4">
                        <strong>Dicatat oleh:</strong><br>
                        {{ $case->recorder?->name ?? '-' }}
                    </div>
                </div>
                <hr>
                <strong>Deskripsi:</strong>
                <p class="mt-2 mb-0">{{ $case->deskripsi }}</p>
                @if($case->bukti_path)
                    <hr>
                    <strong>Bukti:</strong>
                    <p class="mt-2 mb-0">
                        <a href="{{ asset('storage/' . $case->bukti_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-paperclip"></i> Lihat Bukti
                        </a>
                    </p>
                @endif
            </div>
            <div class="card-footer text-muted small">
                Dibuat {{ $case->created_at->format('d/m/Y H:i') }}
                @if($case->updated_at->ne($case->created_at))
                    · Diperbarui {{ $case->updated_at->format('d/m/Y H:i') }}
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4 border-left-{{ $case->student->total_poin >= 0 ? 'success' : 'warning' }}">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Poin Siswa</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">
                    {{ $case->student->total_poin > 0 ? '+' : '' }}{{ $case->student->total_poin }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
