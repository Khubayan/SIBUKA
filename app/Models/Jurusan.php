<?php

namespace App\Models;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    /** @use HasFactory<\Database\Factories\JurusanFactory> */
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = ['kode', 'nama'];

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }
}
