<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition(): array
    {
        return [
            'jurusan_id' => Jurusan::factory(),
            'nama' => 'XII RPL 1',
            'tingkat' => 'XII',
            'tahun_ajaran' => '2025/2026',
        ];
    }
}
