<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBukuRelasi extends Model
{
    use HasFactory;

    protected $table = 'kategoribuku_relasi';
    protected $primaryKey = 'KategoriBukuID';
    protected $fillable = [
        'BukuID',
        'KategoriID'
    ];

    // RELASI BUKU
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'BukuID');
    }

    // RELASI KATEGORI
    public function kategori()
    {
        return $this->belongsTo(KategoriBuku::class, 'KategoriID', 'KategoriID');
    }
}
