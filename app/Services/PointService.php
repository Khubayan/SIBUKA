<?php

namespace App\Services;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Events\PointThresholdReached;
use App\Models\CaseRecord;
use App\Models\PointLog;
use App\Models\Student;

class PointService
{
    public function normalizePoint(CaseCategory $kategori, int $poin): int
    {
        return match ($kategori) {
            CaseCategory::Pelanggaran => -abs($poin),
            CaseCategory::Prestasi => abs($poin),
        };
    }

    public function applyCasePoints(CaseRecord $case): void
    {
        if ($case->status !== CaseStatus::Confirmed) {
            return;
        }

        $case->loadMissing('student');
        $student = $case->student;
        $poinSebelum = $student->total_poin;
        $poinSesudah = $poinSebelum + $case->poin;

        $student->update(['total_poin' => $poinSesudah]);

        PointLog::create([
            'student_id' => $student->id,
            'case_record_id' => $case->id,
            'poin_sebelum' => $poinSebelum,
            'poin_perubahan' => $case->poin,
            'poin_sesudah' => $poinSesudah,
            'keterangan' => "Kasus: {$case->judul}",
            'created_at' => now(),
        ]);

        $this->checkThreshold($student, $poinSesudah);
    }

    public function rollbackCasePoints(CaseRecord $case): void
    {
        if ($case->status !== CaseStatus::Confirmed) {
            return;
        }

        $case->loadMissing('student');
        $student = $case->student;
        $poinSebelum = $student->total_poin;
        $poinSesudah = $poinSebelum - $case->poin;

        $student->update(['total_poin' => $poinSesudah]);

        PointLog::create([
            'student_id' => $student->id,
            'case_record_id' => $case->id,
            'poin_sebelum' => $poinSebelum,
            'poin_perubahan' => -$case->poin,
            'poin_sesudah' => $poinSesudah,
            'keterangan' => "Rollback kasus: {$case->judul}",
            'created_at' => now(),
        ]);
    }

    public function recalculateStudentPoints(Student $student): int
    {
        $total = $student->cases()
            ->where('status', CaseStatus::Confirmed)
            ->sum('poin');

        $student->update(['total_poin' => $total]);

        return $total;
    }

    protected function checkThreshold(Student $student, int $totalPoin): void
    {
        $thresholds = collect(config('sibuka.point_thresholds'))
            ->sortBy(fn ($value) => $value);

        foreach ($thresholds as $level => $value) {
            if ($totalPoin <= $value) {
                event(new PointThresholdReached($student, $level, $totalPoin));

                break;
            }
        }
    }
}
