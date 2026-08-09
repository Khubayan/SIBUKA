@extends('layouts.siswa-portal')

@section('title', 'Biodata')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Input Biodata</h1>
    <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                @if($student->foto)
                    <img src="{{ asset('storage/' . $student->foto) }}" class="img-fluid rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover" alt="">
                @else
                    <img src="{{ asset('assets/sb-admin-2/img/undraw_profile.svg') }}" class="mb-3" style="width:120px" alt="">
                @endif
                <h5 class="font-weight-bold">{{ $student->nama }}</h5>
                <p class="text-muted mb-1">NIS: {{ $student->nis }}</p>
                <p class="text-muted mb-0">{{ $student->kelas?->nama }} — {{ $student->kelas?->jurusan?->nama }}</p>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body small text-muted">
                <p class="mb-1"><i class="fas fa-info-circle text-primary"></i> Data NIS, nama, kelas hanya dapat diubah oleh Guru BK/Admin.</p>
                <p class="mb-0">Anda dapat memperbarui alamat, nomor HP, dan foto profil.</p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Biodata</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('siswa.biodata.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" value="{{ $student->nama }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>NIS / NISN</label>
                        <input type="text" class="form-control" value="{{ $student->nis }} / {{ $student->nisn ?? '-' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin / Tanggal Lahir</label>
                        <input type="text" class="form-control" value="{{ $student->jenis_kelamin }} / {{ $student->tanggal_lahir->format('d/m/Y') }}" disabled>
                    </div>

                    <x-form-input name="alamat" label="Alamat" :value="old('alamat', $student->alamat)" />
                    <x-form-input name="no_hp" label="No. HP" :value="old('no_hp', $student->no_hp)" />

                    <div class="form-group">
                        <label for="foto">Foto Profil</label>
                        <input type="file" name="foto" id="foto" class="form-control-file @error('foto') is-invalid @enderror" accept="image/*">
                        @error('foto')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">JPG/PNG, max 2MB.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Biodata
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
