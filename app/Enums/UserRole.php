<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case GuruBk = 'guru_bk';
    case WaliKelas = 'wali_kelas';
    case OrangTua = 'orang_tua';
    case Siswa = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::GuruBk => 'Guru BK',
            self::WaliKelas => 'Wali Kelas',
            self::OrangTua => 'Orang Tua',
            self::Siswa => 'Siswa',
        };
    }
}
