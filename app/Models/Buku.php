<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'BukuID';
    protected $fillable = [
        'Judul',
        'Penulis',
        'Penerbit',
        'TahunTerbit',
        'Cover',
        'Deskripsi',
        'Stok'
    ];

    // RELASI PEMINJAMAN
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'BukuID');
    }

    // RELASI ULASAN
    public function ulasan()
    {
        return $this->hasMany(UlasanBuku::class, 'BukuID');
    }

    // RELASI KOLEKSI
    public function koleksi()
    {
        return $this->hasMany(KoleksiPribadi::class, 'BukuID');
    }

    // RELASI KATEGORI
    public function kategoriRelasi()
    {
        return $this->hasMany(KategoriBukuRelasi::class, 'BukuID', 'BukuID');
    }
}
