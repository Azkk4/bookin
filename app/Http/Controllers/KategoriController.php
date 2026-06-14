<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\KategoriBuku;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'popular');

        $query = KategoriBuku::withCount('kategoriRelasi');

        switch ($sort) {

            case 'name_asc':
                $query->orderBy('NamaKategori');
                break;

            case 'name_desc':
                $query->orderByDesc('NamaKategori');
                break;

            case 'newest':
                $query->orderByDesc('created_at');
                break;

            case 'oldest':
                $query->orderBy('created_at');
                break;

            case 'updated':
                $query->orderByDesc('updated_at');
                break;

            case 'popular':
            default:
                $query->orderByDesc('kategori_relasi_count')
                    ->orderBy('NamaKategori');
                break;
        }

        return response()->json(
            $query->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'NamaKategori' => [
                'required',
                'string',
                'max:255',
            ]
        ]);

        $namaKategori = ucwords(strtolower(trim($request->NamaKategori)));

        $exists = KategoriBuku::whereRaw(
            'LOWER(NamaKategori) = ?',
            [strtolower($namaKategori)]
        )->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori sudah ada'
            ], 422);
        }

        $kategori = KategoriBuku::create([
            'NamaKategori' => $namaKategori
        ]);

        return response()->json([
            'success' => true,
            'data' => $kategori
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NamaKategori' => [
                'required',
                'string',
                'max:255',
            ]
        ]);

        $namaKategori = ucwords(strtolower(trim($request->NamaKategori)));

        $exists = KategoriBuku::whereRaw(
            'LOWER(NamaKategori) = ?',
            [strtolower($namaKategori)]
        )
        ->where('KategoriID', '!=', $id)
        ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori sudah ada'
            ], 422);
        }

        $kategori = KategoriBuku::findOrFail($id);

        $kategori->update([
            'NamaKategori' => $namaKategori
        ]);

        return response()->json([
            'success' => true
        ]);

    }

    public function destroy($id)
    {
        $kategori = KategoriBuku::findOrFail($id);

        $jumlah = $kategori->kategoriRelasi()->count();

        if ($jumlah > 0) {
            return response()->json([
                'success' => false,
                'message' => "Kategori masih digunakan oleh {$jumlah} buku."
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
