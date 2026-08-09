# Manual Pengguna — SIBUKA

Sistem Informasi Buku Kasus — SMK TI Bali Global Klungkung

## 1. Login

1. Buka alamat sistem (contoh: `https://sibuka.smk-tibaliglobal.sch.id/login`)
2. Masukkan email dan password
3. Klik **Login**

Setelah login, Anda diarahkan ke:
- **Admin / Guru BK / Wali Kelas** → Dashboard
- **Orang Tua** → Portal Orang Tua
- **Siswa** → Portal Siswa

Lupa password? Hubungi administrator sekolah (reset tidak tersedia publik).

## 2. Admin

### Master Data
- **Jurusan** — kelola jurusan (RPL, TKJ, DKV, dll.)
- **Kelas** — kelola kelas dan assign wali kelas
- **Jenis Kasus** — master pelanggaran & prestasi beserta poin default
- **Pengguna** — kelola akun dan role (admin, guru BK, wali kelas, orang tua, siswa)

### Siswa & Kasus
Sama seperti Guru BK (lihat §3), plus akses laporan seluruh sekolah.

## 3. Guru BK

### Data Siswa
- **Lihat** daftar siswa, cari by NIS/nama/kelas
- **Tambah** siswa manual atau **import CSV** (download template terlebih dahulu)
- **Edit** / **hapus** (soft delete) data siswa
- **Detail siswa** — profil, total poin, riwayat kasus

### Manajemen Kasus
1. Klik **Kasus** → **Tambah Kasus**
2. Pilih siswa, jenis kasus (poin auto-fill), isi judul & deskripsi
3. Pilih status:
   - **Draft** — belum mempengaruhi poin
   - **Confirmed** — poin langsung dihitung
4. Opsional: upload bukti (jpg, png, pdf, max 2MB)
5. Kasus draft dapat dikonfirmasi dari halaman detail

**Poin:**
- Pelanggaran = negatif (contoh: -10)
- Prestasi = positif (contoh: +20)
- Threshold perhatian: warning (-25), SP1 (-50), SP2 (-75), SP3 (-100)

### Laporan
- **Per siswa** — PDF atau CSV
- **Per kelas** — PDF atau CSV
- **Seluruh sekolah** — PDF atau CSV (admin & guru BK)
- Filter rentang tanggal tersedia

### Notifikasi
Notifikasi in-app saat siswa mencapai threshold poin kritis.

## 4. Wali Kelas

- **Dashboard** — statistik hanya untuk kelas yang diampu
- **Siswa** — lihat siswa kelas sendiri (tidak bisa tambah/edit)
- **Kasus** — lihat kasus siswa kelas sendiri (read-only)
- **Laporan** — per siswa dan per kelas (tidak akses laporan sekolah)

## 5. Siswa (Portal)

Siswa merupakan pengguna yang dapat mengakses informasi terkait dirinya sendiri.

### Hak akses
- **Login** — masuk ke Portal Siswa
- **Input biodata** — perbarui alamat, nomor HP, dan foto profil
- **Info pelanggaran** — lihat riwayat pelanggaran yang sudah dikonfirmasi
- **Info sisa poin** — lihat total poin, ringkasan prestasi/pelanggaran, dan riwayat perubahan poin

### Cara menggunakan
1. Login → masuk ke **Portal Siswa**
2. **Beranda** — ringkasan sisa poin dan pelanggaran terbaru
3. **Biodata** — isi/perbarui alamat, no. HP, foto (NIS, nama, kelas hanya bisa diubah oleh Admin/Guru BK)
4. **Pelanggaran** — daftar lengkap catatan pelanggaran
5. **Sisa Poin** — detail poin saat ini, batas perhatian (warning, SP1–SP3), dan log perubahan poin

Siswa **tidak** dapat melihat data siswa lain atau mengakses menu admin.

Akun demo: `siswa@sibuka.local` — password: `password` (setelah `DemoDataSeeder`).

## 6. Orang Tua (Portal)

1. Login → masuk ke **Portal Orang Tua**
2. **Daftar Anak** — lihat nama, kelas, total poin
3. **Detail** — riwayat kasus anak (read-only)
4. **Notifikasi** — pemberitahuan saat ada kasus baru dikonfirmasi
5. **Download laporan PDF** riwayat anak dari portal

Orang tua **tidak** dapat melihat data siswa lain.

## 7. Import Siswa (CSV)

Format kolom: `nis, nisn, nama, jenis_kelamin, tanggal_lahir, kelas, alamat, no_hp`

- `jenis_kelamin`: L atau P
- `kelas`: nama kelas harus **exact match** dengan data di sistem (contoh: `XII RPL 1`)
- `tanggal_lahir`: format `YYYY-MM-DD`

## 8. Kontak Dukungan

Hubungi tim IT SMK TI Bali Global Klungkung untuk:
- Reset password
- Bug atau error sistem
- Permintaan akun baru
