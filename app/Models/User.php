<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isGuruBk(): bool
    {
        return $this->role === UserRole::GuruBk;
    }

    public function isWaliKelas(): bool
    {
        return $this->role === UserRole::WaliKelas;
    }

    public function isOrangTua(): bool
    {
        return $this->role === UserRole::OrangTua;
    }

    public function isSiswa(): bool
    {
        return $this->role === UserRole::Siswa;
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentModel::class, 'user_id');
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }
}
