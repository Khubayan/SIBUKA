<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('the application redirects guests to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('authenticated admin can access dashboard', function () {
    $user = User::factory()->create([
        'email' => 'admin@test.local',
        'role' => UserRole::Admin,
        'password' => Hash::make('password'),
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});
