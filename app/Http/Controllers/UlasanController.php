<?php

namespace App\Http\Controllers;

use App\Models\UlasanBuku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UlasanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'BukuID' => 'required',
            'Rating' => 'required|integer|min:1|max:5',
            'Ulasan' => 'nullable|string'
        ]);

        // User Dummy
        $user = User::find(1);

        $ulasan = UlasanBuku::create([
            'UserID' => $user->UserID,
            'BukuID' => $request->BukuID,
            'Rating' => $request->Rating,
            'Ulasan' => $request->Ulasan,
        ]);

        return response()->json([
            'success' => true,

            'data' => [

                'nama' => $user->NamaLengkap,

                'username' => $user->Username,

                'foto' => $user->Foto
                    ? asset('storage/' . $user->Foto)
                    : asset('images/default-profile.png'),

                'rating' => $ulasan->Rating,

                'ulasan' => $ulasan->Ulasan,

                'created_at' => $ulasan->created_at->diffForHumans(),
            ]
        ]);
    }
}