<?php

namespace App\Policies;

use App\Enums\CaseStatus;
use App\Models\CaseRecord;
use App\Models\User;

class CasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuruBk() || $user->isWaliKelas();
    }

    public function view(User $user, CaseRecord $caseRecord): bool
    {
        if ($user->isAdmin() || $user->isGuruBk()) {
            return true;
        }

        if ($user->isWaliKelas()) {
            $caseRecord->loadMissing('student');

            return $user->kelasWali()
                ->where('id', $caseRecord->student?->kelas_id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGuruBk();
    }

    public function update(User $user, CaseRecord $caseRecord): bool
    {
        return $user->isAdmin() || $user->isGuruBk();
    }

    public function delete(User $user, CaseRecord $caseRecord): bool
    {
        return $user->isAdmin() || $user->isGuruBk();
    }

    public function confirm(User $user, CaseRecord $caseRecord): bool
    {
        return ($user->isAdmin() || $user->isGuruBk())
            && $caseRecord->status === CaseStatus::Draft;
    }
}
