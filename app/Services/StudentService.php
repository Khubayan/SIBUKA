<?php

namespace App\Services;

use App\Enums\StudentStatus;
use App\Models\Kelas;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StudentService
{
    public function store(array $data, ?UploadedFile $foto = null): Student
    {
        if ($foto) {
            $data['foto'] = $foto->store('students', 'public');
        }

        $data['total_poin'] = 0;

        return Student::create($data);
    }

    public function update(Student $student, array $data, ?UploadedFile $foto = null): Student
    {
        if ($foto) {
            $data['foto'] = $foto->store('students', 'public');
        }

        $student->update($data);

        return $student->fresh();
    }

    public function updateBiodata(Student $student, array $data, ?UploadedFile $foto = null): Student
    {
        $updates = [
            'alamat' => $data['alamat'] ?? null,
            'no_hp' => $data['no_hp'] ?? null,
        ];

        if ($foto) {
            $updates['foto'] = $foto->store('students', 'public');
        }

        $student->update($updates);

        return $student->fresh();
    }

    public function importFromCsv(UploadedFile $file): int
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);

            throw new \InvalidArgumentException('File CSV kosong.');
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);
        $imported = 0;

        DB::transaction(function () use ($handle, $header, &$imported) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count(array_filter($row)) === 0) {
                    continue;
                }

                $data = array_combine($header, array_pad($row, count($header), null));

                if (! $data || empty($data['nis']) || empty($data['nama'])) {
                    continue;
                }

                $kelas = Kelas::where('nama', trim($data['kelas'] ?? ''))->first();

                if (! $kelas) {
                    throw new \InvalidArgumentException("Kelas '{$data['kelas']}' tidak ditemukan.");
                }

                Student::updateOrCreate(
                    ['nis' => trim($data['nis'])],
                    [
                        'nisn' => ! empty($data['nisn']) ? trim($data['nisn']) : null,
                        'nama' => trim($data['nama']),
                        'jenis_kelamin' => strtoupper(trim($data['jenis_kelamin'] ?? 'L')),
                        'tanggal_lahir' => Carbon::parse($data['tanggal_lahir']),
                        'kelas_id' => $kelas->id,
                        'alamat' => $data['alamat'] ?? null,
                        'no_hp' => $data['no_hp'] ?? null,
                        'status' => StudentStatus::Aktif,
                        'total_poin' => 0,
                    ]
                );

                $imported++;
            }
        });

        fclose($handle);

        return $imported;
    }
}
