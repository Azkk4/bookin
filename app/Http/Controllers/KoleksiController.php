<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KoleksiPribadi;

class KoleksiController extends Controller
{
    public function toggle(Request $request)
    {
        $userId = auth()->id();

        $koleksi = KoleksiPribadi::where('UserID', $userId)
            ->where('BukuID', $request->BukuID)
            ->first();

        // kalau sudah ada -> hapus
        if ($koleksi) {

            $koleksi->delete();

            return response()->json([
                'status' => 'removed'
            ]);
        }

        // kalau belum ada -> tambah
        KoleksiPribadi::create([
            'UserID' => $userId,
            'BukuID' => $request->BukuID
        ]);

        return response()->json([
            'status' => 'added'
        ]);
    }


    public function deleteSelected(Request $request)
{
    $userId = auth()->id();

    KoleksiPribadi::where('UserID', $userId)
        ->whereIn('BukuID', $request->BukuID)
        ->delete();

    return response()->json([
        'success' => true
    ]);
}
}