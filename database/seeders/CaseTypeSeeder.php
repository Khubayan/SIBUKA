<?php

namespace Database\Seeders;

use App\Enums\CaseCategory;
use App\Models\CaseType;
use Illuminate\Database\Seeder;

class CaseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['kode' => 'PLG-001', 'nama' => 'Terlambat masuk sekolah', 'kategori' => CaseCategory::Pelanggaran, 'poin_default' => -5],
            ['kode' => 'PLG-002', 'nama' => 'Tidak memakai seragam lengkap', 'kategori' => CaseCategory::Pelanggaran, 'poin_default' => -10],
            ['kode' => 'PLG-003', 'nama' => 'Bolos pelajaran', 'kategori' => CaseCategory::Pelanggaran, 'poin_default' => -25],
            ['kode' => 'PLG-004', 'nama' => 'Berkelahi', 'kategori' => CaseCategory::Pelanggaran, 'poin_default' => -50],
            ['kode' => 'PRS-001', 'nama' => 'Juara lomba tingkat kelas', 'kategori' => CaseCategory::Prestasi, 'poin_default' => 20],
            ['kode' => 'PRS-002', 'nama' => 'Juara lomba tingkat sekolah', 'kategori' => CaseCategory::Prestasi, 'poin_default' => 50],
            ['kode' => 'PRS-003', 'nama' => 'Siswa teladan bulanan', 'kategori' => CaseCategory::Prestasi, 'poin_default' => 30],
        ];

        foreach ($types as $type) {
            CaseType::updateOrCreate(
                ['kode' => $type['kode']],
                [
                    'nama' => $type['nama'],
                    'kategori' => $type['kategori'],
                    'poin_default' => $type['poin_default'],
                    'is_active' => true,
                ]
            );
        }
    }
}
