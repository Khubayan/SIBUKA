<?php

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'nis', 'nisn', 'nama', 'jenis_kelamin', 'tanggal_lahir',
        'alamat', 'no_hp', 'foto', 'kelas_id', 'total_poin', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'status' => StudentStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CaseRecord::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentModel::class, 'student_parent', 'student_id', 'parent_id');
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(PointLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', StudentStatus::Aktif);
    }

    public function scopeInKelas($query, int $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }
}
