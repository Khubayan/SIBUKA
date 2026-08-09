<?php

use App\Enums\UserRole;
use App\Models\User;

it('prevents wali kelas from creating cases', function () {
    $waliKelas = User::factory()->waliKelas()->create();

    $this->actingAs($waliKelas)
        ->post(route('cases.store'), [])
        ->assertForbidden();
});

it('prevents orang tua from accessing student management', function () {
    $ortu = User::factory()->orangTua()->create();

    $this->actingAs($ortu)
        ->get(route('students.index'))
        ->assertForbidden();
});

it('prevents orang tua from accessing dashboard', function () {
    $ortu = User::factory()->orangTua()->create();

    $this->actingAs($ortu)
        ->get(route('dashboard'))
        ->assertForbidden();
});

it('prevents siswa from accessing dashboard', function () {
    $siswa = User::factory()->siswa()->create();

    $this->actingAs($siswa)
        ->get(route('dashboard'))
        ->assertForbidden();
});

it('prevents guru bk from accessing user management', function () {
    $guruBk = User::factory()->guruBk()->create();

    $this->actingAs($guruBk)
        ->get(route('users.index'))
        ->assertForbidden();
});
