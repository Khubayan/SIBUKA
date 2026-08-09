<?php

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\CaseRecord;
use App\Models\CaseType;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\User;

function createSiswaPortalUser(): array
{
    $jurusan = Jurusan::create(['kode' => 'SIS', 'nama' => 'Siswa Jurusan']);
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII SIS 1',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    $user = User::factory()->create([
        'role' => UserRole::Siswa,
        'email' => 'siswa-portal@test.local',
    ]);

    $student = Student::create([
        'user_id' => $user->id,
        'nis' => 'SISPORT01',
        'nama' => 'Siswa Portal Test',
        'jenis_kelamin' => 'L',
        'tanggal_lahir' => '2008-03-10',
        'kelas_id' => $kelas->id,
        'alamat' => 'Klungkung',
        'no_hp' => '0811111111',
        'status' => StudentStatus::Aktif,
        'total_poin' => -15,
    ]);

    return [$user, $student];
}

test('siswa can access portal index', function () {
    [$user] = createSiswaPortalUser();

    $this->actingAs($user)
        ->get(route('siswa.index'))
        ->assertOk()
        ->assertSee('Portal Siswa');
});

test('siswa can view biodata form', function () {
    [$user, $student] = createSiswaPortalUser();

    $this->actingAs($user)
        ->get(route('siswa.biodata.edit'))
        ->assertOk()
        ->assertSee('Input Biodata')
        ->assertSee($student->nama);
});

test('siswa can update biodata', function () {
    [$user, $student] = createSiswaPortalUser();

    $this->actingAs($user)
        ->put(route('siswa.biodata.update'), [
            'alamat' => 'Alamat Baru Siswa',
            'no_hp' => '08999999999',
        ])
        ->assertRedirect(route('siswa.biodata.edit'))
        ->assertSessionHas('success');

    expect($student->fresh()->alamat)->toBe('Alamat Baru Siswa');
    expect($student->fresh()->no_hp)->toBe('08999999999');
});

test('siswa can view pelanggaran page', function () {
    [$user, $student] = createSiswaPortalUser();
    $admin = User::factory()->admin()->create();
    $caseType = CaseType::create([
        'kode' => 'PLG-SIS', 'nama' => 'Pelanggaran Siswa', 'kategori' => CaseCategory::Pelanggaran,
        'poin_default' => -10, 'is_active' => true,
    ]);

    CaseRecord::create([
        'student_id' => $student->id,
        'case_type_id' => $caseType->id,
        'recorded_by' => $admin->id,
        'kategori' => CaseCategory::Pelanggaran,
        'judul' => 'Terlambat',
        'deskripsi' => 'Terlambat masuk kelas',
        'poin' => -10,
        'tanggal_kejadian' => now(),
        'status' => CaseStatus::Confirmed,
    ]);

    $this->actingAs($user)
        ->get(route('siswa.pelanggaran'))
        ->assertOk()
        ->assertSee('Terlambat');
});

test('siswa can view sisa poin page', function () {
    [$user, $student] = createSiswaPortalUser();

    $this->actingAs($user)
        ->get(route('siswa.poin'))
        ->assertOk()
        ->assertSee('Info Sisa Poin')
        ->assertSee((string) $student->total_poin);
});

test('siswa cannot access admin dashboard', function () {
    [$user] = createSiswaPortalUser();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

test('siswa cannot access orang tua portal', function () {
    [$user] = createSiswaPortalUser();

    $this->actingAs($user)
        ->get(route('portal.index'))
        ->assertForbidden();
});

test('login redirects siswa to student portal', function () {
    [$user] = createSiswaPortalUser();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('siswa.index'));
});
