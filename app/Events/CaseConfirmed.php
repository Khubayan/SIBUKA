<?php

namespace App\Events;

use App\Models\CaseRecord;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CaseConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(public CaseRecord $case) {}
}
