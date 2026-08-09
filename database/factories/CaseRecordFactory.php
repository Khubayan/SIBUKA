<?php

namespace Database\Factories;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Models\CaseRecord;
use App\Models\CaseType;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseRecord>
 */
class CaseRecordFactory extends Factory
{
    protected $model = CaseRecord::class;

    public function definition(): array
    {
        $kategori = fake()->randomElement(CaseCategory::cases());

        return [
            'student_id' => Student::factory(),
            'case_type_id' => CaseType::factory(),
            'recorded_by' => User::factory(),
            'kategori' => $kategori,
            'judul' => fake()->sentence(4),
            'deskripsi' => fake()->paragraph(),
            'poin' => $kategori === CaseCategory::Pelanggaran ? -10 : 10,
            'tanggal_kejadian' => fake()->dateTimeBetween('-3 months', 'now'),
            'status' => CaseStatus::Draft,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => CaseStatus::Confirmed]);
    }

    public function pelanggaran(): static
    {
        return $this->state(fn () => [
            'kategori' => CaseCategory::Pelanggaran,
            'poin' => -10,
        ]);
    }

    public function prestasi(): static
    {
        return $this->state(fn () => [
            'kategori' => CaseCategory::Prestasi,
            'poin' => 20,
        ]);
    }
}
