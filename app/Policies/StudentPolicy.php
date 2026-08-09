<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuruBk() || $user->isWaliKelas();
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isAdmin() || $user->isGuruBk()) {
            return true;
        }

        if ($user->isWaliKelas()) {
            return $user->kelasWali()->where('id', $student->kelas_id)->exists();
        }

        if ($user->isOrangTua()) {
            return $user->parentProfile
                ?->students()
                ->where('students.id', $student->id)
                ->exists() ?? false;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGuruBk();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isAdmin() || $user->isGuruBk();
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isAdmin() || $user->isGuruBk();
    }

    public function import(User $user): bool
    {
        return $user->isAdmin() || $user->isGuruBk();
    }
}
