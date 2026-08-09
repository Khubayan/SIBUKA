<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\PointThresholdReached;
use App\Models\User;
use App\Notifications\PointThresholdNotification;

class NotifyGuruBkOnThreshold
{
    public function handle(PointThresholdReached $event): void
    {
        $users = User::query()
            ->where('is_active', true)
            ->whereIn('role', [UserRole::Admin, UserRole::GuruBk])
            ->get();

        foreach ($users as $user) {
            $user->notify(new PointThresholdNotification(
                $event->student,
                $event->level,
                $event->totalPoin
            ));
        }
    }
}
