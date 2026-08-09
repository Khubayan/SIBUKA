<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Aktif = 'aktif';
    case Lulus = 'lulus';
    case Pindah = 'pindah';
    case Dropout = 'dropout';
}
