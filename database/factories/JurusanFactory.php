<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jurusan>
 */
class JurusanFactory extends Factory
{
    protected $model = Jurusan::class;

    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->lexify('???'),
            'nama' => fake()->words(2, true),
        ];
    }
}
