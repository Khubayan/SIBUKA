<?php

namespace App\Enums;

enum CaseStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Archived = 'archived';
}
