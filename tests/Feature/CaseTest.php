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

function createCaseTestStudent(): Student
{
    $jurusan = Jurusan::create(['kode' => 'CS', 'nama' => 'Case Jurusan']);
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII CS 1',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    return Student::create([
        'nis' => 'CS001',
        'nama' => 'Siswa Kasus',
        'jenis_kelamin' => 'L',
        'tanggal_lahir' => '2008-01-01',
        'kelas_id' => $kelas->id,
        'status' => \App\Enums\StudentStatus::Aktif,
        'total_poin' => 0,
    ]);
}

function createCaseType(): CaseType
{
    return CaseType::create([
        'kode' => 'PLG-TST',
        'nama' => 'Test Pelanggaran',
        'kategori' => CaseCategory::Pelanggaran,
        'poin_default' => -10,
        'is_active' => true,
    ]);
}

test('admin can access cases index', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('cases.index'))
        ->assertOk();
});

test('wali kelas can view cases for their class only', function () {
    $jurusan = Jurusan::create(['kode' => 'WK', 'nama' => 'WK Jurusan']);
    $wali = User::factory()->create(['role' => UserRole::WaliKelas]);

    $kelasWali = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII WK 1',
        'tingkat' => 'XII',
        'wali_kelas_id' => $wali->id,
        'tahun_ajaran' => '2025/2026',
    ]);

    $kelasLain = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII WK 2',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    $studentWali = Student::create([
        'nis' => 'WKC01', 'nama' => 'Siswa Kasus Wali', 'jenis_kelamin' => 'L',
        'tanggal_lahir' => '2008-01-01', 'kelas_id' => $kelasWali->id,
        'status' => \App\Enums\StudentStatus::Aktif,
    ]);

    $studentLain = Student::create([
        'nis' => 'WKC02', 'nama' => 'Siswa Kasus Lain', 'jenis_kelamin' => 'P',
        'tanggal_lahir' => '2008-01-01', 'kelas_id' => $kelasLain->id,
        'status' => \App\Enums\StudentStatus::Aktif,
    ]);

    $admin = User::factory()->admin()->create();
    $caseType = createCaseType();

    CaseRecord::create([
        'student_id' => $studentWali->id,
        'case_type_id' => $caseType->id,
        'recorded_by' => $admin->id,
        'kategori' => CaseCategory::Pelanggaran,
        'judul' => 'Kasus Kelas Wali',
        'deskripsi' => 'Test',
        'poin' => -10,
        'tanggal_kejadian' => now(),
        'status' => CaseStatus::Confirmed,
    ]);

    CaseRecord::create([
        'student_id' => $studentLain->id,
        'case_type_id' => $caseType->id,
        'recorded_by' => $admin->id,
        'kategori' => CaseCategory::Pelanggaran,
        'judul' => 'Kasus Kelas Lain',
        'deskripsi' => 'Test',
        'poin' => -10,
        'tanggal_kejadian' => now(),
        'status' => CaseStatus::Confirmed,
    ]);

    $response = $this->actingAs($wali)->get(route('cases.index'));

    $response->assertOk();
    $response->assertSee('Kasus Kelas Wali');
    $response->assertDontSee('Kasus Kelas Lain');
});

test('draft case does not change student points', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = createCaseTestStudent();
    $caseType = createCaseType();

    $this->actingAs($admin)
        ->post(route('cases.store'), [
            'student_id' => $student->id,
            'case_type_id' => $caseType->id,
            'kategori' => CaseCategory::Pelanggaran->value,
            'judul' => 'Terlambat',
            'deskripsi' => 'Terlambat masuk kelas',
            'poin' => 10,
            'tanggal_kejadian' => now()->format('Y-m-d'),
            'status' => CaseStatus::Draft->value,
        ])
        ->assertRedirect();

    expect($student->fresh()->total_poin)->toBe(0);
});

test('confirmed case changes student points', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = createCaseTestStudent();
    $caseType = createCaseType();

    $this->actingAs($admin)
        ->post(route('cases.store'), [
            'student_id' => $student->id,
            'case_type_id' => $caseType->id,
            'kategori' => CaseCategory::Pelanggaran->value,
            'judul' => 'Terlambat',
            'deskripsi' => 'Terlambat masuk kelas',
            'poin' => 10,
            'tanggal_kejadian' => now()->format('Y-m-d'),
            'status' => CaseStatus::Confirmed->value,
        ])
        ->assertRedirect();

    expect($student->fresh()->total_poin)->toBe(-10);
});

test('confirming draft case updates student points', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = createCaseTestStudent();
    $caseType = createCaseType();

    $case = CaseRecord::create([
        'student_id' => $student->id,
        'case_type_id' => $caseType->id,
        'recorded_by' => $admin->id,
        'kategori' => CaseCategory::Pelanggaran,
        'judul' => 'Draft Kasus',
        'deskripsi' => 'Test',
        'poin' => -10,
        'tanggal_kejadian' => now(),
        'status' => CaseStatus::Draft,
    ]);

    $this->actingAs($admin)
        ->patch(route('cases.confirm', $case))
        ->assertRedirect(route('cases.show', $case));

    expect($student->fresh()->total_poin)->toBe(-10);
    expect($case->fresh()->status)->toBe(CaseStatus::Confirmed);
});

test('deleting confirmed case rolls back points', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student = createCaseTestStudent();
    $student->update(['total_poin' => -10]);
    $caseType = createCaseType();

    $case = CaseRecord::create([
        'student_id' => $student->id,
        'case_type_id' => $caseType->id,
        'recorded_by' => $admin->id,
        'kategori' => CaseCategory::Pelanggaran,
        'judul' => 'Kasus Hapus',
        'deskripsi' => 'Test',
        'poin' => -10,
        'tanggal_kejadian' => now(),
        'status' => CaseStatus::Confirmed,
    ]);

    $this->actingAs($admin)
        ->delete(route('cases.destroy', $case))
        ->assertRedirect(route('cases.index'));

    expect($student->fresh()->total_poin)->toBe(0);
});
