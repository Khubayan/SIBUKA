<?php

use App\Models\CaseRecord;
use App\Models\Student;
use App\Services\PointService;

it('applies negative points for pelanggaran', function () {
    $student = Student::factory()->create(['total_poin' => 0]);
    $case = CaseRecord::factory()->confirmed()->pelanggaran()->create([
        'student_id' => $student->id,
        'poin' => -10,
    ]);

    app(PointService::class)->applyCasePoints($case);

    expect($student->fresh()->total_poin)->toBe(-10);
});

it('applies positive points for prestasi', function () {
    $student = Student::factory()->create(['total_poin' => 0]);
    $case = CaseRecord::factory()->confirmed()->prestasi()->create([
        'student_id' => $student->id,
        'poin' => 20,
    ]);

    app(PointService::class)->applyCasePoints($case);

    expect($student->fresh()->total_poin)->toBe(20);
});

it('rollbacks points on delete', function () {
    $student = Student::factory()->create(['total_poin' => -10]);
    $case = CaseRecord::factory()->confirmed()->pelanggaran()->create([
        'student_id' => $student->id,
        'poin' => -10,
    ]);

    app(PointService::class)->rollbackCasePoints($case);

    expect($student->fresh()->total_poin)->toBe(0);
});

it('normalizes pelanggaran points to negative', function () {
    $service = app(PointService::class);

    expect($service->normalizePoint(\App\Enums\CaseCategory::Pelanggaran, 10))->toBe(-10);
});

it('normalizes prestasi points to positive', function () {
    $service = app(PointService::class);

    expect($service->normalizePoint(\App\Enums\CaseCategory::Prestasi, 20))->toBe(20);
});
