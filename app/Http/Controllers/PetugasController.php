<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;

class PetugasController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Buku::with([
            'kategoriRelasi.kategori',
            'peminjaman',
            'ulasan.user'
        ]);

        if ($request->search) {
            $query->where(
                'Judul',
                'like',
                '%' . $request->search . '%'
            );
        }

        $buku = $query->paginate(10);

        foreach ($buku as $item) {
            $item->ulasan_data = $item->ulasan->map(function ($ulasan) {
                return [
                    'nama' => $ulasan->user->NamaLengkap ?? 'User',
                    'rating' => $ulasan->Rating,
                    'ulasan' => $ulasan->Ulasan,
                    'foto' => $ulasan->user && $ulasan->user->Foto
                        ? asset('storage/profile/' . $ulasan->user->Foto)
                        : asset('images/default-profile.png')
                ];
            });

            $ratingCount = $item->ulasan
                ->groupBy('Rating')
                ->map(fn($group) => $group->count());

            $item->rating_average = round(
                $item->ulasan->avg('Rating') ?? 0,
                1
            );

            $item->rating_distribution = [
                5 => $ratingCount[5] ?? 0,
                4 => $ratingCount[4] ?? 0,
                3 => $ratingCount[3] ?? 0,
                2 => $ratingCount[2] ?? 0,
                1 => $ratingCount[1] ?? 0,
            ];
        }

        $kategori = KategoriBuku::all();

        $totalBuku = Buku::count();

        $bukuDipinjam = Buku::whereHas(
            'peminjaman',
            function ($q) {
                $q->where(
                    'StatusPeminjaman',
                    'Dipinjam'
                );
            }
        )->count();

        $dipinjamBulanIni = Peminjaman::whereMonth(
            'TanggalPeminjaman',
            now()->month
        )->count();

        $telat = Peminjaman::where(
            'StatusPeminjaman',
            'Terlambat'
        )->count();

        return view(
            'petugas.dashboard',
            compact(
                'buku',
                'kategori',
                'totalBuku',
                'bukuDipinjam',
                'dipinjamBulanIni',
                'telat'
            )
        );
    }
}