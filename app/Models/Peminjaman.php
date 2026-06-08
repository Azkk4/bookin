<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'PeminjamanID';
    protected $fillable = [
        'UserID',
        'BukuID',
        'TanggalPeminjaman',
        'TanggalPengembalian',
        'TanggalDikembalikan',
        'StatusPeminjaman'
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