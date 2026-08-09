@php
    $caseType = $caseType ?? null;
    $kategoriOptions = collect($categories)->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all();
@endphp

<x-form-input name="kode" label="Kode" :value="old('kode', $caseType?->kode)" placeholder="PLG-001" required />
<x-form-input name="nama" label="Nama Jenis Kasus" :value="old('nama', $caseType?->nama)" required />
<x-form-select name="kategori" label="Kategori" :options="$kategoriOptions" :selected="old('kategori', $caseType?->kategori?->value)" required />
<x-form-input name="poin_default" label="Poin Default (angka positif)" type="number" :value="old('poin_default', $caseType?->poin_default)" required min="1" />

<div class="form-group">
    <label for="deskripsi">Deskripsi</label>
    <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $caseType?->deskripsi) }}</textarea>
    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@if($caseType)
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" @checked(old('is_active', $caseType->is_active))>
            <label class="custom-control-label" for="is_active">Aktif</label>
        </div>
    </div>
@endif
