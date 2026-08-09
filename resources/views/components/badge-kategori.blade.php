@props(['kategori'])

@if($kategori === 'pelanggaran' || $kategori?->value === 'pelanggaran')
    <span class="badge badge-danger">Pelanggaran</span>
@else
    <span class="badge badge-success">Prestasi</span>
@endif
