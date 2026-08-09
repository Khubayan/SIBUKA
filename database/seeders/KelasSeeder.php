<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $waliKelas = User::where('email', 'walikelas@sibuka.local')->first();

        $kelasData = [
            ['jurusan' => 'RPL', 'nama' => 'XII RPL 1', 'tingkat' => 'XII'],
            ['jurusan' => 'RPL', 'nama' => 'XI RPL 1', 'tingkat' => 'XI'],
            ['jurusan' => 'TKJ', 'nama' => 'XII TKJ 1', 'tingkat' => 'XII'],
            ['jurusan' => 'TKJ', 'nama' => 'XI TKJ 1', 'tingkat' => 'XI'],
        ];

        foreach ($kelasData as $index => $item) {
            $jurusan = Jurusan::where('kode', $item['jurusan'])->first();

            if (! $jurusan) {
                continue;
            }

            Kelas::updateOrCreate(
                [
                    'jurusan_id' => $jurusan->id,
                    'nama' => $item['nama'],
                    'tahun_ajaran' => '2025/2026',
                ],
                [
                    'tingkat' => $item['tingkat'],
                    'wali_kelas_id' => $index === 0 ? $waliKelas?->id : null,
                ]
            );
        }
    }
}
