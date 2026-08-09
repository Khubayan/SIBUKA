<?php

namespace Database\Seeders;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\CaseRecord;
use App\Models\CaseType;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            JurusanSeeder::class,
            CaseTypeSeeder::class,
        ]);

        $jurusanRpl = Jurusan::where('kode', 'RPL')->first();
        $jurusanTkj = Jurusan::where('kode', 'TKJ')->first();
        $jurusanDkv = Jurusan::where('kode', 'DKV')->first();

        if (! $jurusanRpl || ! $jurusanTkj || ! $jurusanDkv) {
            return;
        }

        $waliUsers = [
            User::updateOrCreate(['email' => 'walikelas@sibuka.local'], [
                'name' => 'Wali Kelas RPL', 'password' => Hash::make('password'),
                'role' => UserRole::WaliKelas, 'is_active' => true, 'email_verified_at' => now(),
            ]),
            User::updateOrCreate(['email' => 'walikelas2@sibuka.local'], [
                'name' => 'Wali Kelas TKJ', 'password' => Hash::make('password'),
                'role' => UserRole::WaliKelas, 'is_active' => true, 'email_verified_at' => now(),
            ]),
            User::updateOrCreate(['email' => 'walikelas3@sibuka.local'], [
                'name' => 'Wali Kelas DKV', 'password' => Hash::make('password'),
                'role' => UserRole::WaliKelas, 'is_active' => true, 'email_verified_at' => now(),
            ]),
        ];

        User::updateOrCreate(['email' => 'gurubk2@sibuka.local'], [
            'name' => 'Guru BK 2', 'password' => Hash::make('password'),
            'role' => UserRole::GuruBk, 'is_active' => true, 'email_verified_at' => now(),
        ]);

        $kelasDefinitions = [
            ['jurusan' => $jurusanRpl, 'nama' => 'XII RPL 1', 'tingkat' => 'XII', 'wali' => $waliUsers[0]],
            ['jurusan' => $jurusanRpl, 'nama' => 'XI RPL 1', 'tingkat' => 'XI', 'wali' => null],
            ['jurusan' => $jurusanTkj, 'nama' => 'XII TKJ 1', 'tingkat' => 'XII', 'wali' => $waliUsers[1]],
            ['jurusan' => $jurusanTkj, 'nama' => 'XI TKJ 1', 'tingkat' => 'XI', 'wali' => null],
            ['jurusan' => $jurusanDkv, 'nama' => 'XII DKV 1', 'tingkat' => 'XII', 'wali' => $waliUsers[2]],
            ['jurusan' => $jurusanDkv, 'nama' => 'XI DKV 1', 'tingkat' => 'XI', 'wali' => null],
        ];

        $kelasList = collect();
        foreach ($kelasDefinitions as $def) {
            $kelasList->push(Kelas::updateOrCreate(
                ['jurusan_id' => $def['jurusan']->id, 'nama' => $def['nama'], 'tahun_ajaran' => '2025/2026'],
                ['tingkat' => $def['tingkat'], 'wali_kelas_id' => $def['wali']?->id]
            ));
        }

        $students = collect();
        $nisCounter = 2024100;
        foreach ($kelasList as $kelas) {
            for ($i = 1; $i <= 5; $i++) {
                $nisCounter++;
                $students->push(Student::updateOrCreate(
                    ['nis' => (string) $nisCounter],
                    [
                        'nisn' => '00'.str_pad((string) $nisCounter, 8, '0', STR_PAD_LEFT),
                        'nama' => "Siswa Demo {$nisCounter}",
                        'jenis_kelamin' => $i % 2 === 0 ? 'P' : 'L',
                        'tanggal_lahir' => '2008-0'.(($i % 9) + 1).'-15',
                        'kelas_id' => $kelas->id,
                        'alamat' => 'Klungkung, Bali',
                        'status' => StudentStatus::Aktif,
                        'total_poin' => 0,
                    ]
                ));
            }
        }

        $parentStudents = $students->take(5);
        foreach ($parentStudents->values() as $index => $student) {
            $user = User::updateOrCreate(
                ['email' => 'ortu'.($index + 1).'@sibuka.local'],
                [
                    'name' => 'Orang Tua '.($index + 1),
                    'password' => Hash::make('password'),
                    'role' => UserRole::OrangTua,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $parent = ParentModel::updateOrCreate(
                ['user_id' => $user->id],
                ['nama' => $user->name, 'no_hp' => '08123456789'.$index, 'hubungan' => 'wali']
            );

            $parent->students()->sync([$student->id]);
        }

        $admin = User::where('email', 'admin@sibuka.local')->first();
        $caseTypes = CaseType::active()->get();
        if (! $admin || $caseTypes->isEmpty()) {
            return;
        }

        $caseTitles = [
            ['kategori' => CaseCategory::Pelanggaran, 'judul' => 'Terlambat masuk sekolah', 'poin' => -5],
            ['kategori' => CaseCategory::Pelanggaran, 'judul' => 'Tidak memakai seragam', 'poin' => -10],
            ['kategori' => CaseCategory::Pelanggaran, 'judul' => 'Bolos pelajaran', 'poin' => -25],
            ['kategori' => CaseCategory::Prestasi, 'judul' => 'Juara lomba kelas', 'poin' => 20],
            ['kategori' => CaseCategory::Prestasi, 'judul' => 'Siswa teladan', 'poin' => 30],
        ];

        for ($i = 0; $i < 20; $i++) {
            $student = $students->random();
            $template = $caseTitles[$i % count($caseTitles)];
            $caseType = $caseTypes->firstWhere('kategori', $template['kategori']) ?? $caseTypes->first();

            CaseRecord::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'judul' => $template['judul'].' #'.($i + 1),
                ],
                [
                    'case_type_id' => $caseType->id,
                    'recorded_by' => $admin->id,
                    'kategori' => $template['kategori'],
                    'deskripsi' => 'Data demo UAT kasus #'.($i + 1),
                    'poin' => $template['poin'],
                    'tanggal_kejadian' => now()->subDays(20 - $i),
                    'status' => CaseStatus::Confirmed,
                ]
            );
        }

        foreach ($students as $student) {
            $total = $student->cases()->where('status', CaseStatus::Confirmed)->sum('poin');
            $student->update(['total_poin' => $total]);
        }

        $demoStudent = $students->first();
        if ($demoStudent) {
            $siswaUser = User::updateOrCreate(
                ['email' => 'siswa@sibuka.local'],
                [
                    'name' => $demoStudent->nama,
                    'password' => Hash::make('password'),
                    'role' => UserRole::Siswa,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $demoStudent->update(['user_id' => $siswaUser->id]);
        }
    }
}
