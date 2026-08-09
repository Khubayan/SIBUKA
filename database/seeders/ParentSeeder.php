<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $student = Student::where('nis', '2024001')->first();

        if (! $student) {
            return;
        }

        $user = User::updateOrCreate(
            ['email' => 'ortu@sibuka.local'],
            [
                'name' => 'I Wayan Parent',
                'password' => Hash::make('password'),
                'role' => UserRole::OrangTua,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $parent = ParentModel::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => 'I Wayan Parent',
                'no_hp' => '081234567890',
                'hubungan' => 'ayah',
            ]
        );

        $parent->students()->sync([$student->id]);
    }
}
