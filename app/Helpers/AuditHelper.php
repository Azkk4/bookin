<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditHelper
{
    public static function log(
        $aksi,
        $deskripsi
    ) {
        AuditLog::create([
            'UserID' => auth()->id(),
            'Aksi' => $aksi,
            'Deskripsi' => $deskripsi,
            'CreatedAt' => now(),
        ]);
    }
}