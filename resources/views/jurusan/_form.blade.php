<x-form-input name="kode" label="Kode Jurusan" :value="old('kode', $jurusan->kode ?? '')" placeholder="Contoh: RPL" required maxlength="10" />
<x-form-input name="nama" label="Nama Jurusan" :value="old('nama', $jurusan->nama ?? '')" placeholder="Contoh: Rekayasa Perangkat Lunak" required />
