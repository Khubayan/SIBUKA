<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = [
            ['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak'],
            ['kode' => 'TKJ', 'nama' => 'Teknik Komputer dan Jaringan'],
            ['kode' => 'DKV', 'nama' => 'Desain Komunikasi Visual'],
            ['kode' => 'MM', 'nama' => 'Multimedia'],
        ];

        foreach ($jurusan as $item) {
            Jurusan::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
