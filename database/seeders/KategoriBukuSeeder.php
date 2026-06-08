<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriBuku;

class KategoriBukuSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Novel',
            'Fantasi',
            'Sejarah',
            'Ilmiah',
            'Pendidikan',
            'Roman',
            'Fantasi',
            'Horror',
            'Bahasa',
            'Biografi',
            'Agama',
        ];

        foreach ($kategori as $item) {
            KategoriBuku::create([
                'NamaKategori' => $item
            ]);
        }
    }
}