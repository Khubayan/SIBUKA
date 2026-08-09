<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            JurusanSeeder::class,
            CaseTypeSeeder::class,
            KelasSeeder::class,
            StudentSeeder::class,
            ParentSeeder::class,
        ]);
    }
}
