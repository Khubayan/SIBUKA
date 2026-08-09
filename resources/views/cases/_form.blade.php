@php
    $case = $case ?? null;
    $kategoriOptions = collect($categories)->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all();
    $statusOptions = collect($statuses)->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)])->all();
    $studentOptions = $students->mapWithKeys(fn ($s) => [$s->id => "{$s->nama} ({$s->kelas?->nama})"])->all();
@endphp

<x-form-select name="student_id" label="Siswa" :options="$studentOptions" :selected="old('student_id', $case?->student_id ?? $selectedStudentId)" required />

<div class="form-group">
    <label for="case_type_id">Jenis Kasus <span class="text-danger">*</span></label>
    <select name="case_type_id" id="case_type_id" class="form-control @error('case_type_id') is-invalid @enderror" required>
        <option value="">Pilih jenis kasus...</option>
        @foreach($caseTypes as $type)
            <option value="{{ $type->id }}"
                data-poin="{{ abs($type->poin_default) }}"
                data-kategori="{{ $type->kategori->value }}"
                @selected(old('case_type_id', $case?->case_type_id) == $type->id)>
                {{ $type->kode }} — {{ $type->nama }}
            </option>
        @endforeach
    </select>
    @error('case_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<x-form-input name="judul" label="Judul Kasus" :value="old('judul', $case?->judul)" required />

<div class="form-group">
    <label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $case?->deskripsi) }}</textarea>
    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-row">
    <div class="col-md-4">
        <x-form-select name="kategori" label="Kategori" :options="$kategoriOptions" :selected="old('kategori', $case?->kategori?->value)" required />
    </div>
    <div class="col-md-4">
        <x-form-input name="poin" label="Poin (angka positif)" type="number" :value="old('poin', $case ? abs($case->poin) : '')" required min="1" max="1000" />
    </div>
    <div class="col-md-4">
        <x-form-input name="tanggal_kejadian" label="Tanggal Kejadian" type="date" :value="old('tanggal_kejadian', $case?->tanggal_kejadian?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
    </div>
</div>

<x-form-select name="status" label="Status" :options="$statusOptions" :selected="old('status', $case?->status?->value ?? 'draft')" required />

<div class="form-group">
    <label for="bukti">Bukti (opsional)</label>
    <input type="file" name="bukti" id="bukti" class="form-control-file @error('bukti') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
    @error('bukti')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    @if($case?->bukti_path)
        <small class="text-muted d-block mt-1">Bukti saat ini: <a href="{{ asset('storage/' . $case->bukti_path) }}" target="_blank">Lihat file</a></small>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const caseTypeSelect = document.getElementById('case_type_id');
    const poinInput = document.getElementById('poin');
    const kategoriSelect = document.getElementById('kategori');

    if (!caseTypeSelect) return;

    caseTypeSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.value) return;

        const poin = selected.dataset.poin;
        const kategori = selected.dataset.kategori;

        if (poin && poinInput) {
            poinInput.value = poin;
        }

        if (kategori && kategoriSelect) {
            kategoriSelect.value = kategori;
        }
    });
});
</script>
@endpush
