<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\Student;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuruBk() || $user->isWaliKelas();
    }

    public function student(User $user, Student $student): bool
    {
        return app(StudentPolicy::class)->view($user, $student);
    }

    public function classReport(User $user, Kelas $kelas): bool
    {
        if ($user->isAdmin() || $user->isGuruBk()) {
            return true;
        }

        if ($user->isWaliKelas()) {
            return $user->kelasWali()->where('id', $kelas->id)->exists();
        }

        return false;
    }

    public function school(User $user): bool
    {
        return $user->isAdmin() || $user->isGuruBk();
    }
}
