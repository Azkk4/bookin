<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\KoleksiPribadi;
use App\Models\Peminjaman;
use App\Models\KategoriBuku;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        // semua buku
        $buku = Buku::with([
            'kategoriRelasi.kategori',
            'ulasan.user'
        ])->get();

        // rating
        foreach ($buku as $item) {
            $ratingCount = $item->ulasan
                ->groupBy('Rating')
                ->map(fn($group) => $group->count());

            $item->rating_average = round($item->ulasan->avg('Rating') ?? 0, 1);

            $item->rating_distribution = [
                5 => $ratingCount[5] ?? 0,
                4 => $ratingCount[4] ?? 0,
                3 => $ratingCount[3] ?? 0,
                2 => $ratingCount[2] ?? 0,
                1 => $ratingCount[1] ?? 0,
            ];
        }

        // kategori
        $kategori = KategoriBuku::all();

        // koleksi user
        $koleksiIDs = KoleksiPribadi::where('UserID', $userId)
            ->pluck('BukuID');

        $koleksi = Buku::with([
            'kategoriRelasi.kategori',
            'ulasan.user'
        ])
        ->whereIn('BukuID', $koleksiIDs)
        ->get();

        // history
        $history = Peminjaman::with('buku')
            ->where('UserID', $userId)
            ->latest()
            ->get();

        // buku dipinjam
        $dipinjam = Peminjaman::with('buku')
            ->where('UserID', $userId)
            ->where('StatusPeminjaman', 'Dipinjam')
            ->latest()
            ->get();

        // Mengecek buku yang dipinjam
        $sedangDipinjam = Peminjaman::where('UserID', $userId)
            ->where('StatusPeminjaman', 'Dipinjam')
            ->pluck('BukuID')
            ->toArray();

        $peminjamanAktif = Peminjaman::where('UserID', $userId)
            ->where('StatusPeminjaman', 'Dipinjam')
            ->pluck('PeminjamanID', 'BukuID');

        return view('dashboard', compact(
            'buku',
            'koleksi',
            'kategori',
            'history',
            'dipinjam',
            'sedangDipinjam',
            'peminjamanAktif'
        ));
    }

    public function guest()
    {
        $buku = Buku::with([
            'kategoriRelasi.kategori',
            'ulasan.user'
        ])->get();

        foreach ($buku as $item) {

            $ratingCount = $item->ulasan
                ->groupBy('Rating')
                ->map(fn($group) => $group->count());

            $item->rating_average =
                round($item->ulasan->avg('Rating') ?? 0, 1);

            $item->rating_distribution = [
                5 => $ratingCount[5] ?? 0,
                4 => $ratingCount[4] ?? 0,
                3 => $ratingCount[3] ?? 0,
                2 => $ratingCount[2] ?? 0,
                1 => $ratingCount[1] ?? 0,
            ];
        }

        $kategori = KategoriBuku::all();

        $koleksi = collect();

        $sedangDipinjam = [];

        $peminjamanAktif = collect();

        return view('dashboard', compact(
            'buku',
            'kategori',
            'koleksi',
            'sedangDipinjam',
            'peminjamanAktif'
        ) + [
            'history' => collect(),
            'dipinjam' => collect(),
        ]);
    }
}