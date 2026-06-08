<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UlasanBuku extends Model
{
    use HasFactory;

    protected $table = 'ulasanbuku';
    protected $primaryKey = 'UlasanID';
    protected $fillable = [
        'UserID',
        'BukuID',
        'Ulasan',
        'Rating'
    ];

    // RELASI USER
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    // RELASI BUKU
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'BukuID');
    }
}