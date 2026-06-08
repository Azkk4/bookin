<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'auditlog';

    protected $primaryKey = 'LogID';

    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'Aksi',
        'Deskripsi',
        'CreatedAt'
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'UserID',
            'UserID'
        );
    }
}