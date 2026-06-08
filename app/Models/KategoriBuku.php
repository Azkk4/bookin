<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBuku extends Model
{
    use HasFactory;

    protected $table = 'kategoribuku';
    protected $primaryKey = 'KategoriID';
    protected $fillable = [
        'NamaKategori'
    ];

    // RELASI
    public function kategoriRelasi()
    {
        return $this->hasMany(KategoriBukuRelasi::class, 'KategoriID');
    }
}