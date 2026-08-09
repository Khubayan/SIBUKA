<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sibuka.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'gurubk@sibuka.local'],
            [
                'name' => 'Guru BK',
                'password' => Hash::make('password'),
                'role' => UserRole::GuruBk,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'walikelas@sibuka.local'],
            [
                'name' => 'Wali Kelas',
                'password' => Hash::make('password'),
                'role' => UserRole::WaliKelas,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
