# Checklist UAT — SIBUKA

Gunakan dokumen ini saat User Acceptance Testing dengan Guru BK, Wali Kelas, dan Orang Tua.

## Persiapan UAT

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoDataSeeder
php artisan serve
```

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@sibuka.local` | `password` |
| Guru BK | `gurubk@sibuka.local` | `password` |
| Guru BK 2 | `gurubk2@sibuka.local` | `password` |
| Wali Kelas RPL | `walikelas@sibuka.local` | `password` |
| Wali Kelas TKJ | `walikelas2@sibuka.local` | `password` |
| Wali Kelas DKV | `walikelas3@sibuka.local` | `password` |
| Orang Tua | `ortu1@sibuka.local` … `ortu5@sibuka.local` | `password` |
| Orang Tua (seed dasar) | `ortu@sibuka.local` | `password` |
| Siswa | `siswa@sibuka.local` | `password` |

## Skenario UAT

| No | Skenario | Role | Langkah | Expected | Pass? |
|----|----------|------|---------|----------|-------|
| 1 | Login valid | Semua | Buka `/login`, masukkan email & password benar | Redirect ke dashboard atau portal | [ ] |
| 2 | Login invalid | Semua | Password salah | Pesan error, tetap di halaman login | [ ] |
| 3 | Dashboard statistik | Admin/BK | Buka `/dashboard` | Widget statistik & tren kasus tampil | [ ] |
| 4 | Dashboard scoped | Wali Kelas | Login wali kelas | Hanya data kelas yang diampu | [ ] |
| 5 | Tambah siswa | Admin/BK | Siswa → Tambah → simpan | Siswa tersimpan, muncul di index | [ ] |
| 6 | Import CSV siswa | Admin/BK | Download template → isi → import | Siswa bulk insert berhasil | [ ] |
| 7 | Wali kelas lihat siswa | Wali Kelas | Buka `/students` | Hanya siswa kelas sendiri | [ ] |
| 8 | Wali kelas tidak CRUD siswa | Wali Kelas | Coba tambah siswa | 403 Forbidden | [ ] |
| 9 | Tambah kasus pelanggaran (draft) | Admin/BK | Kasus → tambah, status draft | Poin siswa tidak berubah | [ ] |
| 10 | Konfirmasi kasus | Admin/BK | Konfirmasi kasus draft | Poin siswa berkurang | [ ] |
| 11 | Tambah kasus prestasi | Admin/BK | Kasus prestasi, confirmed | Poin siswa bertambah | [ ] |
| 12 | Wali kelas lihat kasus | Wali Kelas | Buka `/cases` | Hanya kasus kelas sendiri, tidak bisa tambah | [ ] |
| 13 | Filter kasus | Admin/BK | Filter by kelas/kategori/tanggal | Data sesuai filter | [ ] |
| 14 | Upload bukti kasus | Admin/BK | Upload jpg/png/pdf ≤ 2MB | File tersimpan, bisa dilihat | [ ] |
| 15 | Laporan PDF siswa | Admin/BK/Wali | Laporan → pilih siswa → PDF | File PDF terdownload | [ ] |
| 16 | Laporan CSV | Admin/BK | Export CSV | File CSV terdownload | [ ] |
| 17 | Portal orang tua | Orang Tua | Login → portal | Daftar anak tampil | [ ] |
| 18 | Riwayat anak | Orang Tua | Klik detail anak | Riwayat kasus read-only | [ ] |
| 19 | Isolasi data ortu | Orang Tua | Akses URL siswa lain | 403 Forbidden | [ ] |
| 20 | Notifikasi kasus | Orang Tua | Admin konfirmasi kasus anak | Notifikasi in-app muncul | [ ] |
| 21 | CRUD master data | Admin | Jurusan, kelas, jenis kasus, user | CRUD berfungsi | [ ] |
| 22 | Guru BK tidak kelola user | Guru BK | Akses `/users` | 403 Forbidden | [ ] |

## Catatan Bug / Feedback

| No UAT | Tester | Tanggal | Deskripsi | Severity | Status |
|--------|--------|---------|-----------|----------|--------|
| | | | | | |

**Severity:** Critical / Major / Minor / Cosmetic

## Sign-off

| Role | Nama | Tanggal | Tanda |
|------|------|---------|-------|
| Guru BK | | | |
| Wali Kelas | | | |
| Admin IT | | | |
