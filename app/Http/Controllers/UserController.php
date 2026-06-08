<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Helpers\AuditHelper;

class UserController extends Controller
{
    public function updatePhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = User::findOrFail($id);

        // hapus foto lama
        if ($user->Foto &&
            Storage::disk('public')->exists('user/' . $user->Foto))
        {
            Storage::disk('public')
                ->delete('user/' . $user->Foto);
        }

        $file = $request->file('photo');

        $filename =
            Str::uuid() .
            '.' .
            $file->getClientOriginalExtension();

        $file->storeAs(
            'user',
            $filename,
            'public'
        );

        $user->Foto = $filename;
        $user->save();

        AuditHelper::log(
            'Ubah Foto',
            'Mengubah foto user ' .
            $user->Username
        );

        return response()->json([
            'success' => true,
            'photo' => asset(
                'storage/user/' . $filename
            )
        ]);
    }

    public function changeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // tidak boleh mengubah role diri sendiri
        if (auth()->id() == $user->UserID) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengubah role akun sendiri'
            ], 403);
        }

        // jika admin terakhir akan diturunkan
        if (
            $user->Role === 'Admin'
            && $request->Role !== 'Admin'
        ) {

            $adminCount = User::where(
                'Role',
                'Admin'
            )->where(
                'Status',
                'Aktif'
            )->count();

            if ($adminCount <= 1) {

                return response()->json([
                    'success' => false,
                    'message' => 'Minimal harus ada 1 admin aktif'
                ], 422);

            }
        }

        $user->Role = $request->Role;
        $user->save();

        AuditHelper::log(
            'Ubah Role',
            'Mengubah role user ' .
            $user->Username .
            ' menjadi ' .
            $request->Role
        );

        return response()->json([
            'success' => true
        ]);
    }
}
