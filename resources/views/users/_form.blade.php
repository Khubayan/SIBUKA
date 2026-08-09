@php
    $roleOptions = collect($roles)->mapWithKeys(fn ($r) => [$r->value => $r->label()])->all();
    $isEdit = $isEdit ?? false;
    $user = $user ?? null;
@endphp

<x-form-input name="name" label="Nama Lengkap" :value="old('name', $user?->name)" required />
<x-form-input name="email" label="Email" type="email" :value="old('email', $user?->email)" required />
<x-form-select name="role" label="Role" :options="$roleOptions" :selected="old('role', $user?->role?->value)" required />

<x-form-input name="password" label="{{ $isEdit ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}" type="password" :required="!$isEdit" />
<x-form-input name="password_confirmation" label="Konfirmasi Password" type="password" :required="!$isEdit" />

@if($isEdit)
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1"
                @checked(old('is_active', $user?->is_active ?? true))>
            <label class="custom-control-label" for="is_active">Akun Aktif</label>
        </div>
    </div>
@endif

<div id="wali-kelas-fields" style="display:none;">
    <hr>
    <h6 class="text-primary">Data Wali Kelas</h6>
    <x-form-input name="nip" label="NIP" :value="old('nip', $user?->teacher?->nip)" placeholder="Opsional" />
</div>

<div id="orang-tua-fields" style="display:none;">
    <hr>
    <h6 class="text-primary">Data Orang Tua</h6>
    <x-form-input name="no_hp" label="No. HP" :value="old('no_hp', $user?->parentProfile?->no_hp)" />
    <x-form-select name="hubungan" label="Hubungan" :options="['ayah' => 'Ayah', 'ibu' => 'Ibu', 'wali' => 'Wali']" :selected="old('hubungan', $user?->parentProfile?->hubungan ?? 'wali')" />

    <div class="form-group">
        <label for="student_ids">Hubungkan ke Siswa</label>
        <select name="student_ids[]" id="student_ids" class="form-control" multiple size="5">
            @forelse($students as $student)
                <option value="{{ $student->id }}" @selected(in_array($student->id, old('student_ids', $selectedStudentIds)))>
                    {{ $student->nis }} — {{ $student->nama }} ({{ $student->kelas?->nama ?? '-' }})
                </option>
            @empty
                <option disabled>Belum ada data siswa</option>
            @endforelse
        </select>
        <small class="form-text text-muted">Tahan Ctrl untuk memilih lebih dari satu siswa.</small>
    </div>
</div>

<div id="siswa-fields" style="display:none;">
    <hr>
    <h6 class="text-primary">Data Siswa</h6>
    <div class="form-group">
        <label for="student_id">Hubungkan ke Data Siswa <span class="text-danger">*</span></label>
        <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror">
            <option value="">— Pilih Siswa —</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id', $selectedStudentId) == $student->id)>
                    {{ $student->nis }} — {{ $student->nama }} ({{ $student->kelas?->nama ?? '-' }})
                </option>
            @endforeach
        </select>
        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="form-text text-muted">Satu akun siswa terhubung ke satu data siswa.</small>
    </div>
</div>
