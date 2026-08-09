<?php

namespace App\Models;

use App\Enums\CaseCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseType extends Model
{
    /** @use HasFactory<\Database\Factories\CaseTypeFactory> */
    use HasFactory;

    protected $fillable = ['kode', 'nama', 'kategori', 'poin_default', 'deskripsi', 'is_active'];

    protected function casts(): array
    {
        return [
            'kategori' => CaseCategory::class,
            'is_active' => 'boolean',
        ];
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CaseRecord::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, CaseCategory $category)
    {
        return $query->where('kategori', $category);
    }
}
