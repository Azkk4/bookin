<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'UserID';
    protected $fillable = [
        'Username',
        'Password',
        'Email',
        'NamaLengkap',
        'Alamat',
        'Foto',
        'Role',
        'Status'
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->attributes['Password'];
    }

    public function getAuthIdentifierName()
    {
        return 'UserID';
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['UserID'];
    }

    // RELASI PEMINJAMAN
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'UserID');
    }

    // RELASI ULASAN
    public function ulasan()
    {
        return $this->hasMany(UlasanBuku::class, 'UserID');
    }

    // RELASI KOLEKSI
    public function koleksi()
    {
        return $this->hasMany(KoleksiPribadi::class, 'UserID');
    }

    // RELASI AUDIT LOG
    public function auditLogs()
    {
        return $this->hasMany(
            AuditLog::class,
            'UserID',
            'UserID'
        );
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        ];
    }
}
