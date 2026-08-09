<?php

use App\Enums\UserRole;
use App\Models\Jurusan;
use App\Models\User;

test('admin can access jurusan index', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('jurusan.index'))
        ->assertOk();
});

test('guru bk cannot access jurusan management', function () {
    $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

    $this->actingAs($guruBk)
        ->get(route('jurusan.index'))
        ->assertForbidden();
});

test('admin can create jurusan', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->post(route('jurusan.store'), [
            'kode' => 'TST',
            'nama' => 'Jurusan Test',
        ])
        ->assertRedirect(route('jurusan.index'));

    expect(Jurusan::where('kode', 'TST')->exists())->toBeTrue();
});

test('guru bk can view case types index', function () {
    $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

    $this->actingAs($guruBk)
        ->get(route('case-types.index'))
        ->assertOk();
});

test('guru bk cannot create case type', function () {
    $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

    $this->actingAs($guruBk)
        ->get(route('case-types.create'))
        ->assertForbidden();
});
