<?php

namespace Database\Factories;

use App\Enums\StudentStatus;
use App\Models\Kelas;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('2024###'),
            'nisn' => fake()->unique()->numerify('00########'),
            'nama' => fake()->name(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'tanggal_lahir' => fake()->dateTimeBetween('-20 years', '-15 years'),
            'alamat' => fake()->address(),
            'no_hp' => fake()->phoneNumber(),
            'kelas_id' => Kelas::factory(),
            'total_poin' => 0,
            'status' => StudentStatus::Aktif,
        ];
    }
}
