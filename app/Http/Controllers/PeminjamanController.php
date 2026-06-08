<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'BukuID' => 'required',
            'TanggalPeminjaman' => 'required|date',
            'Durasi' => 'required|integer|min:1|max:7',
        ]);

        $buku = Buku::findOrFail($request->BukuID);

        // cek stok
        if ($buku->Stok <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stok buku habis'
            ]);
        }

        $tanggalPinjam = Carbon::parse(
            $request->TanggalPeminjaman
        );

        $tanggalKembali = $tanggalPinjam
            ->copy()
            ->addDays((int) $request->Durasi);

        // cek apakah sudah pinjam
        $userId = auth()->id();
        $cek = Peminjaman::where('UserID', $userId)
            ->where('BukuID', $buku->BukuID)
            ->where('StatusPeminjaman', 'Dipinjam')
            ->exists();

        if ($cek) {
            return response()->json([
                'success' => false,
                'message' => 'Buku ini sedang kamu pinjam'
            ]);
        }

        // simpan
        $peminjaman = Peminjaman::create([
            'UserID' => $userId,
            'BukuID' => $request->BukuID,
            'TanggalPeminjaman' => $tanggalPinjam,
            'TanggalPengembalian' => $tanggalKembali,
            'StatusPeminjaman' => 'Dipinjam'
        ]);

        // kurangi stok
        $buku->decrement('Stok');

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dipinjam',

            'data' => [
                'peminjaman_id' => $peminjaman->PeminjamanID,

                'judul' => $buku->Judul,
                'penulis' => $buku->Penulis,
                'penerbit' => $buku->Penerbit,
                'tahun' => $buku->TahunTerbit,

                'cover' => asset(
                    'storage/books/' . $buku->Cover
                ),

                'tanggal_pinjam' =>
                    $tanggalPinjam->format('d/m/Y'),

                'durasi' =>
                    $request->Durasi . ' Hari',

                'tanggal_kembali' =>
                    $tanggalKembali->format('d/m/Y'),

                'status' => 'Sedang dipinjam',

                'stok' => $buku->fresh()->Stok,

                'isDipinjam' => true
            ]
        ]);
    }

    public function pengembalian($id)
{
    $peminjaman = Peminjaman::with('buku')
        ->findOrFail($id);

    if ($peminjaman->StatusPeminjaman === 'Dikembalikan') {

        return response()->json([
            'success' => false,
            'message' => 'Buku sudah dikembalikan'
        ]);
    }

    $peminjaman->update([

        'StatusPeminjaman' => 'Dikembalikan',

        'TanggalDikembalikan' => now()

    ]);

    if ($peminjaman->buku) {
        $peminjaman->buku->increment('Stok');
    }

    return response()->json([
        'success' => true,

        'message' => 'Buku berhasil dikembalikan',

        'data' => [
            'BukuID' => $peminjaman->BukuID,
            'stok' => $peminjaman->buku->fresh()->Stok,
            'isDipinjam' => false
        ]
    ]);
}
}