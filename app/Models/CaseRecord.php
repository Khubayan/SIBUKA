<?php

namespace App\Models;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseRecord extends Model
{
    /** @use HasFactory<\Database\Factories\CaseRecordFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'case_records';

    protected $fillable = [
        'student_id', 'case_type_id', 'recorded_by', 'kategori',
        'judul', 'deskripsi', 'poin', 'tanggal_kejadian',
        'bukti_path', 'status',
    ];

    protected function casts(): array
    {
        return [
            'kategori' => CaseCategory::class,
            'status' => CaseStatus::class,
            'tanggal_kejadian' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function caseType(): BelongsTo
    {
        return $this->belongsTo(CaseType::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function isConfirmed(): bool
    {
        return $this->status === CaseStatus::Confirmed;
    }
}
