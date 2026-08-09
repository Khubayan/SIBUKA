@php
    $kelas = $kelas ?? null;
    $jurusanOptions = $jurusanList->mapWithKeys(fn ($j) => [$j->id => $j->kode . ' — ' . $j->nama])->all();
    $waliOptions = $waliKelasList->mapWithKeys(fn ($u) => [$u->id => $u->name])->all();
    $tingkatOptions = ['X' => 'X', 'XI' => 'XI', 'XII' => 'XII'];
@endphp

<x-form-select name="jurusan_id" label="Jurusan" :options="$jurusanOptions" :selected="old('jurusan_id', $kelas?->jurusan_id)" required />
<x-form-input name="nama" label="Nama Kelas" :value="old('nama', $kelas?->nama)" placeholder="Contoh: XII RPL 1" required />
<x-form-select name="tingkat" label="Tingkat" :options="$tingkatOptions" :selected="old('tingkat', $kelas?->tingkat)" required />
<x-form-select name="wali_kelas_id" label="Wali Kelas" :options="$waliOptions" :selected="old('wali_kelas_id', $kelas?->wali_kelas_id)" placeholder="— Tidak ada —" />
<x-form-input name="tahun_ajaran" label="Tahun Ajaran" :value="old('tahun_ajaran', $kelas?->tahun_ajaran ?? '2025/2026')" placeholder="2025/2026" required />
