<?php

namespace Database\Seeders;

use App\Enums\StudentStatus;
use App\Models\Kelas;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::where('nama', 'XII RPL 1')->first();

        if (! $kelas) {
            return;
        }

        $students = [
            ['nis' => '2024001', 'nisn' => '0051234567', 'nama' => 'I Made Aditya Pratama', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2008-03-15'],
            ['nis' => '2024002', 'nisn' => '0051234568', 'nama' => 'Ni Luh Ayu Seruni', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2008-07-22'],
            ['nis' => '2024003', 'nisn' => '0051234569', 'nama' => 'Kadek Budi Santosa', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2008-01-10'],
            ['nis' => '2024004', 'nisn' => '0051234570', 'nama' => 'Putu Sri Wahyuni', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2008-11-05'],
            ['nis' => '2024005', 'nisn' => '0051234571', 'nama' => 'I Gusti Ngurah Rai', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2008-05-28'],
        ];

        foreach ($students as $data) {
            Student::updateOrCreate(
                ['nis' => $data['nis']],
                array_merge($data, [
                    'kelas_id' => $kelas->id,
                    'alamat' => 'Klungkung, Bali',
                    'status' => StudentStatus::Aktif,
                    'total_poin' => 0,
                ])
            );
        }
    }
}
