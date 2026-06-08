<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Buku;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        Buku::create([
            'Judul' => 'Laut Bercerita',
            'Penulis' => 'Leila S. Chudori',
            'Penerbit' => 'Gramedia',
            'TahunTerbit' => 2017,
            'Cover' => 'laut-bercerita.jpg',
            'Deskripsi' => 'Novel ini mengisahkan kehidupan sekelompok aktivis mahasiswa pada masa Orde Baru di Indonesia.',
            'Stok' => 3
        ]);
    }
}