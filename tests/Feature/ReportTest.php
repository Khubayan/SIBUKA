<?php

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Enums\UserRole;
use App\Models\CaseRecord;
use App\Models\CaseType;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\User;
use App\Services\ReportService;

test('admin can access reports index', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('reports.index'))
        ->assertOk();
});

test('wali kelas can access reports index', function () {
    $wali = User::factory()->create(['role' => UserRole::WaliKelas]);

    $this->actingAs($wali)
        ->get(route('reports.index'))
        ->assertOk();
});

test('wali kelas cannot access school report', function () {
    $wali = User::factory()->create(['role' => UserRole::WaliKelas]);

    $this->actingAs($wali)
        ->get(route('reports.school', ['format' => 'pdf']))
        ->assertForbidden();
});

test('admin can download student report pdf', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = createReportStudent();
    createConfirmedCase($student, $admin);

    $response = $this->actingAs($admin)
        ->get(route('reports.student', ['student' => $student, 'format' => 'pdf']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('admin can download student report csv', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = createReportStudent();
    createConfirmedCase($student, $admin);

    $response = $this->actingAs($admin)
        ->get(route('reports.student', ['student' => $student, 'format' => 'csv']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('text/csv');
});

test('report service filters cases by date range', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = createReportStudent();

    createConfirmedCase($student, $admin, '2025-01-15');
    createConfirmedCase($student, $admin, '2025-06-15');

    $cases = app(ReportService::class)->getStudentCases($student, '2025-01-01', '2025-03-31');

    expect($cases)->toHaveCount(1);
});

test('dashboard scopes data for wali kelas', function () {
    $jurusan = Jurusan::create(['kode' => 'DB', 'nama' => 'Dash Jurusan']);
    $wali = User::factory()->create(['role' => UserRole::WaliKelas]);

    $kelasWali = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII DB 1',
        'tingkat' => 'XII',
        'wali_kelas_id' => $wali->id,
        'tahun_ajaran' => '2025/2026',
    ]);

    $kelasLain = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII DB 2',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    Student::create([
        'nis' => 'DB001', 'nama' => 'Siswa Wali', 'jenis_kelamin' => 'L',
        'tanggal_lahir' => '2008-01-01', 'kelas_id' => $kelasWali->id,
        'status' => \App\Enums\StudentStatus::Aktif,
    ]);

    Student::create([
        'nis' => 'DB002', 'nama' => 'Siswa Lain', 'jenis_kelamin' => 'P',
        'tanggal_lahir' => '2008-01-01', 'kelas_id' => $kelasLain->id,
        'status' => \App\Enums\StudentStatus::Aktif,
    ]);

    $response = $this->actingAs($wali)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewHas('stats', fn ($stats) => $stats['total_siswa'] === 1);
});

test('dashboard stat cards count pelanggaran and prestasi separately by tanggal kejadian', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = createReportStudent();

    CaseRecord::factory()->prestasi()->create([
        'student_id' => $student->id,
        'tanggal_kejadian' => now(),
    ]);
    CaseRecord::factory()->pelanggaran()->create([
        'student_id' => $student->id,
        'tanggal_kejadian' => now(),
    ]);
    CaseRecord::factory()->prestasi()->create([
        'student_id' => $student->id,
        'tanggal_kejadian' => now()->subMonth(),
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Pelanggaran Bulan Ini');
    $response->assertDontSee('Kasus Bulan Ini');
    $response->assertViewHas('stats', fn ($stats) => $stats['prestasi_bulan_ini'] === 1
        && $stats['pelanggaran_bulan_ini'] === 1);
});

function createReportStudent(): Student
{
    $jurusan = Jurusan::create(['kode' => 'RP', 'nama' => 'Report Jurusan']);
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII RP 1',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    return Student::create([
        'nis' => 'RP001',
        'nama' => 'Siswa Laporan',
        'jenis_kelamin' => 'L',
        'tanggal_lahir' => '2008-01-01',
        'kelas_id' => $kelas->id,
        'status' => \App\Enums\StudentStatus::Aktif,
        'total_poin' => 0,
    ]);
}

function createConfirmedCase(Student $student, User $recorder, ?string $date = null): CaseRecord
{
    $caseType = CaseType::create([
        'kode' => 'PLG-'.uniqid(),
        'nama' => 'Test Report',
        'kategori' => CaseCategory::Pelanggaran,
        'poin_default' => -10,
        'is_active' => true,
    ]);

    return CaseRecord::create([
        'student_id' => $student->id,
        'case_type_id' => $caseType->id,
        'recorded_by' => $recorder->id,
        'kategori' => CaseCategory::Pelanggaran,
        'judul' => 'Kasus Laporan',
        'deskripsi' => 'Test laporan',
        'poin' => -10,
        'tanggal_kejadian' => $date ?? now()->format('Y-m-d'),
        'status' => CaseStatus::Confirmed,
    ]);
}
