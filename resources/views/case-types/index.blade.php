@extends('layouts.app')

@section('title', 'Jenis Kasus')

@section('content')
<x-page-heading title="Jenis Kasus" :createRoute="$canManage ? route('case-types.create') : null" createLabel="Tambah Jenis Kasus" />

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Jenis Kasus</h6>
        <form method="GET" class="form-inline mt-2 mt-md-0">
            <select name="kategori" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <option value="pelanggaran" @selected(request('kategori') === 'pelanggaran')>Pelanggaran</option>
                <option value="prestasi" @selected(request('kategori') === 'prestasi')>Prestasi</option>
            </select>
            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Poin Default</th>
                        <th>Status</th>
                        @if($canManage)<th width="120">Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($caseTypes as $index => $item)
                        <tr>
                            <td>{{ $caseTypes->firstItem() + $index }}</td>
                            <td><strong>{{ $item->kode }}</strong></td>
                            <td>{{ $item->nama }}</td>
                            <td><x-badge-kategori :kategori="$item->kategori" /></td>
                            <td>
                                <span class="badge badge-{{ $item->kategori->value === 'pelanggaran' ? 'danger' : 'success' }}">
                                    {{ $item->kategori->value === 'pelanggaran' ? '-' : '+' }}{{ abs($item->poin_default) }}
                                </span>
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            @if($canManage)
                                <td>
                                    <a href="{{ route('case-types.edit', $item) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('case-types.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jenis kasus ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 7 : 6 }}" class="text-center text-muted">Belum ada data jenis kasus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $caseTypes->links() }}</div>
    </div>
</div>
@endsection
