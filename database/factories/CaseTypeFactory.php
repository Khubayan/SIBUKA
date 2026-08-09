<?php

namespace Database\Factories;

use App\Enums\CaseCategory;
use App\Models\CaseType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseType>
 */
class CaseTypeFactory extends Factory
{
    protected $model = CaseType::class;

    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'nama' => fake()->sentence(3),
            'kategori' => CaseCategory::Pelanggaran,
            'poin_default' => -10,
            'deskripsi' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function prestasi(): static
    {
        return $this->state(fn () => [
            'kategori' => CaseCategory::Prestasi,
            'poin_default' => 20,
        ]);
    }
}
