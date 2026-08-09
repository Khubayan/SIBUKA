<?php

namespace App\Services;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Models\CaseRecord;
use App\Models\Kelas;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    public function getStudentCases(Student $student, ?string $from = null, ?string $to = null): Collection
    {
        return $student->cases()
            ->with(['caseType', 'recorder'])
            ->when($from, fn ($q) => $q->whereDate('tanggal_kejadian', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('tanggal_kejadian', '<=', $to))
            ->where('status', CaseStatus::Confirmed)
            ->orderBy('tanggal_kejadian')
            ->get();
    }

    public function getClassCases(Kelas $kelas, ?string $from = null, ?string $to = null, ?string $kategori = null): Collection
    {
        return CaseRecord::query()
            ->with(['student', 'caseType'])
            ->whereHas('student', fn ($q) => $q->where('kelas_id', $kelas->id))
            ->when($from, fn ($q) => $q->whereDate('tanggal_kejadian', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('tanggal_kejadian', '<=', $to))
            ->when($kategori, fn ($q) => $q->where('kategori', $kategori))
            ->where('status', CaseStatus::Confirmed)
            ->orderBy('tanggal_kejadian')
            ->get();
    }

    public function getSchoolCases(
        ?string $from = null,
        ?string $to = null,
        ?int $jurusanId = null,
        ?string $tingkat = null,
        ?string $kategori = null
    ): Collection {
        return CaseRecord::query()
            ->with(['student.kelas.jurusan', 'caseType'])
            ->whereHas('student.kelas', function ($q) use ($jurusanId, $tingkat) {
                $q->when($jurusanId, fn ($q) => $q->where('jurusan_id', $jurusanId))
                    ->when($tingkat, fn ($q) => $q->where('tingkat', $tingkat));
            })
            ->when($from, fn ($q) => $q->whereDate('tanggal_kejadian', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('tanggal_kejadian', '<=', $to))
            ->when($kategori, fn ($q) => $q->where('kategori', $kategori))
            ->where('status', CaseStatus::Confirmed)
            ->orderBy('tanggal_kejadian')
            ->get();
    }

    public function monthlyTrend(Builder $caseQuery, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        $cases = (clone $caseQuery)
            ->whereYear('tanggal_kejadian', $year)
            ->get(['tanggal_kejadian', 'kategori']);

        $pelanggaran = array_fill(1, 12, 0);
        $prestasi = array_fill(1, 12, 0);

        foreach ($cases as $case) {
            $month = (int) $case->tanggal_kejadian->format('n');

            if ($case->kategori === CaseCategory::Pelanggaran) {
                $pelanggaran[$month]++;
            } else {
                $prestasi[$month]++;
            }
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'pelanggaran' => array_values($pelanggaran),
            'prestasi' => array_values($prestasi),
        ];
    }

    public function streamStudentCsv(Student $student, Collection $cases): StreamedResponse
    {
        $filename = "laporan-siswa-{$student->nis}.csv";

        return $this->streamCsv($filename, [
            'No', 'Tanggal', 'Kategori', 'Jenis Kasus', 'Judul', 'Poin', 'Dicatat Oleh',
        ], $cases->map(fn ($case, $i) => [
            $i + 1,
            $case->tanggal_kejadian->format('d/m/Y'),
            ucfirst($case->kategori->value),
            $case->caseType?->nama ?? '-',
            $case->judul,
            ($case->poin > 0 ? '+' : '').$case->poin,
            $case->recorder?->name ?? '-',
        ]));
    }

    public function streamClassCsv(Kelas $kelas, Collection $cases): StreamedResponse
    {
        $filename = 'laporan-kelas-'.str_replace(' ', '-', strtolower($kelas->nama)).'.csv';

        return $this->streamCsv($filename, [
            'No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kategori', 'Judul', 'Poin',
        ], $cases->map(fn ($case, $i) => [
            $i + 1,
            $case->tanggal_kejadian->format('d/m/Y'),
            $case->student?->nis ?? '-',
            $case->student?->nama ?? '-',
            ucfirst($case->kategori->value),
            $case->judul,
            ($case->poin > 0 ? '+' : '').$case->poin,
        ]));
    }

    public function streamSchoolCsv(Collection $cases): StreamedResponse
    {
        $filename = 'laporan-sekolah-'.now()->format('Y-m-d').'.csv';

        return $this->streamCsv($filename, [
            'No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Jurusan', 'Kategori', 'Judul', 'Poin',
        ], $cases->map(fn ($case, $i) => [
            $i + 1,
            $case->tanggal_kejadian->format('d/m/Y'),
            $case->student?->nis ?? '-',
            $case->student?->nama ?? '-',
            $case->student?->kelas?->nama ?? '-',
            $case->student?->kelas?->jurusan?->kode ?? '-',
            ucfirst($case->kategori->value),
            $case->judul,
            ($case->poin > 0 ? '+' : '').$case->poin,
        ]));
    }

    protected function streamCsv(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
