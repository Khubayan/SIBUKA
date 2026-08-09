<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'case_record_id', 'poin_sebelum',
        'poin_perubahan', 'poin_sesudah', 'keterangan', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function caseRecord(): BelongsTo
    {
        return $this->belongsTo(CaseRecord::class);
    }
}
