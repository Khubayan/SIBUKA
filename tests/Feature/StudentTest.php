<?php

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\User;

test('admin can access students index', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('students.index'))
        ->assertOk();
});

test('admin can access students create page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('students.create'))
        ->assertOk();
});

test('admin can access cases create page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('cases.create'))
        ->assertOk();
});

test('admin can create student', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $jurusan = Jurusan::create(['kode' => 'TST', 'nama' => 'Test']);
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII TST 1',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    $this->actingAs($admin)
        ->post(route('students.store'), [
            'nis' => '2024999',
            'nama' => 'Siswa Test',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2008-01-01',
            'kelas_id' => $kelas->id,
            'status' => StudentStatus::Aktif->value,
        ])
        ->assertRedirect(route('students.index'));

    expect(Student::where('nis', '2024999')->exists())->toBeTrue();
});

test('wali kelas only sees students in their class', function () {
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

    Student::create([
        'nis' => 'WK001', 'nama' => 'Siswa Kelas Wali', 'jenis_kelamin' => 'L',
        'tanggal_lahir' => '2008-01-01', 'kelas_id' => $kelasWali->id, 'status' => StudentStatus::Aktif,
    ]);

    Student::create([
        'nis' => 'WK002', 'nama' => 'Siswa Kelas Lain', 'jenis_kelamin' => 'P',
        'tanggal_lahir' => '2008-01-01', 'kelas_id' => $kelasLain->id, 'status' => StudentStatus::Aktif,
    ]);

    $response = $this->actingAs($wali)->get(route('students.index'));

    $response->assertOk();
    $response->assertSee('Siswa Kelas Wali');
    $response->assertDontSee('Siswa Kelas Lain');
});

test('wali kelas cannot create student', function () {
    $wali = User::factory()->create(['role' => UserRole::WaliKelas]);
    $jurusan = Jurusan::create(['kode' => 'WL', 'nama' => 'WL Jurusan']);
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII WL 1',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    $this->actingAs($wali)
        ->post(route('students.store'), [
            'nis' => 'WL999',
            'nama' => 'Siswa Baru',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2008-01-01',
            'kelas_id' => $kelas->id,
            'status' => StudentStatus::Aktif->value,
        ])
        ->assertForbidden();
});

test('admin can import students from csv', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $jurusan = Jurusan::create(['kode' => 'IMP', 'nama' => 'Import Jurusan']);
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama' => 'XII IMP 1',
        'tingkat' => 'XII',
        'tahun_ajaran' => '2025/2026',
    ]);

    $csv = "nis,nisn,nama,jenis_kelamin,tanggal_lahir,kelas,alamat,no_hp\n";
    $csv .= "IMP001,0059999999,Siswa Import,L,2008-01-01,{$kelas->nama},Klungkung,08123\n";

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('siswa.csv', $csv);

    $this->actingAs($admin)
        ->post(route('students.import'), ['file' => $file])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Student::where('nis', 'IMP001')->exists())->toBeTrue();
});
