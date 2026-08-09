@php
    $student = $student ?? null;
    $kelasOptions = $kelasList->mapWithKeys(fn ($k) => [$k->id => $k->nama . ' (' . ($k->jurusan?->kode ?? '-') . ')'])->all();
    $statusOptions = collect($statusList)->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)])->all();
@endphp

<div class="row">
    <div class="col-md-6">
        <x-form-input name="nis" label="NIS" :value="old('nis', $student?->nis)" required />
        <x-form-input name="nisn" label="NISN" :value="old('nisn', $student?->nisn)" />
        <x-form-input name="nama" label="Nama Lengkap" :value="old('nama', $student?->nama)" required />
        <x-form-select name="jenis_kelamin" label="Jenis Kelamin" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('jenis_kelamin', $student?->jenis_kelamin)" required />
        <x-form-input name="tanggal_lahir" label="Tanggal Lahir" type="date" :value="old('tanggal_lahir', $student?->tanggal_lahir?->format('Y-m-d'))" required />
    </div>
    <div class="col-md-6">
        <x-form-select name="kelas_id" label="Kelas" :options="$kelasOptions" :selected="old('kelas_id', $student?->kelas_id)" required />
        <x-form-select name="status" label="Status" :options="$statusOptions" :selected="old('status', $student?->status?->value ?? 'aktif')" required />
        <x-form-input name="no_hp" label="No. HP" :value="old('no_hp', $student?->no_hp)" />
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $student?->alamat) }}</textarea>
            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="foto">Foto</label>
            <input type="file" name="foto" id="foto" class="form-control-file @error('foto') is-invalid @enderror" accept="image/*">
            @error('foto')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @if($student?->foto)
                <small class="text-muted">Foto saat ini: {{ basename($student->foto) }}</small>
            @endif
        </div>
    </div>
</div>
