<?php

namespace App\Listeners;

use App\Events\CaseConfirmed;
use App\Notifications\NewCaseNotification;

class SendParentNotification
{
    public function handle(CaseConfirmed $event): void
    {
        $student = $event->case->student()->with('parents.user')->first();

        if (! $student) {
            return;
        }

        foreach ($student->parents as $parent) {
            if ($parent->user) {
                $parent->user->notify(new NewCaseNotification($event->case));
            }
        }
    }
}
