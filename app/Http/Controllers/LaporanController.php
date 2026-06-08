<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Buku;
use App\Models\User;
use App\Models\Peminjaman;

class LaporanController extends Controller
{
    public function preview(Request $request)
{
    $type = $request->type;

    $start = Carbon::parse($request->start)->startOfDay();

    $end = Carbon::parse($request->end)->endOfDay();

    $data = [];
    $summary = [];

    /* =========================
       LAPORAN BUKU
    ========================= */

    if ($type == 'buku') {
    
        $data = Buku::with(
            'kategoriRelasi.kategori'
        )
        ->whereBetween(
            'created_at',
            [$start, $end]
        )
        ->get();

        $dipinjam = Buku::whereHas(
            'peminjaman',
            function ($q) {
                $q->where(
                    'StatusPeminjaman',
                    'Dipinjam'
                );
            }
        )->count();

        $stokKosong = $data->where('Stok', 0)->count();

        $totalJudul = $data->count();

        $summary = [
            'stokKosong' => $stokKosong,
            'dipinjam' => $dipinjam,
            'totalJudul' => $totalJudul,
        ];
    }

    /* =========================
       LAPORAN ANGGOTA
    ========================= */

    elseif ($type == 'anggota') {
        
        $users = User::whereBetween(
            'created_at',
            [$start, $end]
        )->get();

        $data = $users;

        $totalAnggota = $users->count();

        $totalAktif = $users
            ->where('Status', 'Aktif')
            ->count();

        $totalNonaktif = $users
            ->where('Status', 'Nonaktif')
            ->count();

        $totalAdmin = $users
            ->where('Role', 'Admin')
            ->count();

        $totalPetugas = $users
            ->where('Role', 'Petugas')
            ->count();

        $totalPeminjam = $users
            ->where('Role', 'Peminjam')
            ->count();

        $summary = [
            'totalAktif' => $totalAktif,
            'totalNonaktif' => $totalNonaktif,
            'totalAdmin' => $totalAdmin,
            'totalPetugas' => $totalPetugas,
            'totalPeminjam' => $totalPeminjam,
        ];
    }

    /* =========================
       LAPORAN PEMINJAMAN
    ========================= */

    elseif ($type == 'peminjaman') {
        
        $peminjaman = Peminjaman::with([
                'user',
                'buku'
            ])
            ->whereBetween(
                'TanggalPeminjaman',
                [$start, $end]
            )
            ->get();
        
        $data = $peminjaman;

        $total = $peminjaman->count();

        $dipinjam = $peminjaman
            ->where('StatusPeminjaman', 'Dipinjam')
            ->count();

        $dikembalikan = $peminjaman
            ->where('StatusPeminjaman', 'Dikembalikan')
            ->count();

        $terlambat = $peminjaman
            ->where('StatusPeminjaman', 'Terlambat')
            ->count();

        $summary = [
            'dipinjam' => $dipinjam,
            'dikembalikan' => $dikembalikan,
            'terlambat' => $terlambat,
        ];
    }

    $html = view(
        "admin.laporan.$type",
        [
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'total' => $data->count(),
            'summary' => $summary
        ]
    )->render();

    return response()->json([
        'success' => true,
        'html' => $html,
        'total' => $data->count()
    ]);
}
}
