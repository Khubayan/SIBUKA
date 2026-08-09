<?php

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Enums\UserRole;
use App\Models\CaseRecord;
use App\Models\CaseType;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use App\Services\CaseService;
use Illuminate\Support\Facades\Notification;

test('orang tua can access portal index', function () {
    [$ortu] = createPortalParent();

    $this->actingAs($ortu)
        ->get(route('portal.index'))
        ->assertOk()
        ->assertSee('Portal Orang Tua');
});

test('orang tua can view own child cases', function () {
    [$ortu, $student] = createPortalParent();

    CaseRecord::create([
        'student_id' => $student->id,
        'case_type_id' => CaseType::create([
            'kode' => 'PLG-PT', 'nama' => 'Test', 'kategori' => CaseCategory::Pelanggaran,
            'poin_default' => -10, 'is_active' => true,
        ])->id,
        'recorded_by' => User::factory()->admin()->create()->id,
        'kategori' => CaseCategory::Pelanggaran,
        'judul' => 'Kasus Portal',
        'deskripsi' => 'Test',
        'poin' => -10,
        'tanggal_kejadian' => now(),
        'status' => CaseStatus::Confirmed,
    ]);

    $this->actingAs($ortu)
        ->get(route('portal.child', $student))
        ->assertOk()
        ->assertSee('Kasus Portal');
});

test('orang tua cannot view other students', function () {
    [$ortu] = createPortalParent();
    $otherStudent = createOtherStudent();

    $this->actingAs($ortu)
        ->get(route('portal.child', $otherStudent))
        ->assertForbidden();
});

test('orang tua cannot access student management', function () {
    [$ortu] = createPortalParent();

    $this->actingAs($ortu)
        ->get(route('students.index'))
        ->assertForbidden();
});

test('orang tua receives notification when case is confirmed', function () {
    Notification::fake();

    [$ortu, $student] = createPortalParent();
    $admin = User::factory()->admin()->create();
    $caseType = CaseType::create([
        'kode' => 'PLG-NOTIF', 'nama' => 'Notif Test', 'kategori' => CaseCategory::Pelanggaran,
        'poin_default' => -10, 'is_active' => true,
    ]);

    $this->actingAs($admin);

    app(CaseService::class)->create([
        'student_id' => $student->id,
        'case_type_id' => $caseType->id,
        'kategori' => CaseCategory::Pelanggaran->value,
        'judul' => 'Notifikasi Test',
        'deskripsi' => 'Test notifikasi',
        'poin' => 10,
        'tanggal_kejadian' => now()->format('Y-m-d'),
        'status' => CaseStatus::Confirmed->value,
    ]);

    Notification::assertSentTo($ortu, \App\Notifications\NewCaseNotification::class);
});

test('orang tua can download child report pdf', function () {
    [$ortu, $student] = createPortalParent();

    $response = $this->actingAs($ortu)
        ->get(route('portal.child.report', $student));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('login redirects orang tua to portal', function () {
    [$ortu] = createPortalParent();

    $this->post(route('login'), [
        'email' => $ortu->email,
        'password' => 'password',
    ])->assertRedirect(route('portal.index'));
});

function createPortalParent(): array
{
    $jurusan = Jurusan::create(['kode' => 'PT', 'nama' => 'Portal Jurusan']);
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII PT 1',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    $student = Student::create([
        'nis' => 'PT001',
        'nama' => 'Anak Portal',
        'jenis_kelamin' => 'L',
        'tanggal_lahir' => '2008-01-01',
        'kelas_id' => $kelas->id,
        'status' => \App\Enums\StudentStatus::Aktif,
        'total_poin' => 0,
    ]);

    $user = User::factory()->orangTua()->create();
    $parent = ParentModel::create([
        'user_id' => $user->id,
        'nama' => $user->name,
        'hubungan' => 'ayah',
    ]);
    $parent->students()->attach($student->id);

    return [$user, $student, $parent];
}

function createOtherStudent(): Student
{
    $jurusan = Jurusan::create(['kode' => 'OT', 'nama' => 'Other Jurusan']);
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII OT 1',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    return Student::create([
        'nis' => 'OT001',
        'nama' => 'Siswa Lain',
        'jenis_kelamin' => 'P',
        'tanggal_lahir' => '2008-01-01',
        'kelas_id' => $kelas->id,
        'status' => \App\Enums\StudentStatus::Aktif,
        'total_poin' => 0,
    ]);
}
