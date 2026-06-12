<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\User;
use App\Models\AuditLog;
use App\Helpers\AuditHelper;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Buku::with([
            'kategoriRelasi.kategori',
            'peminjaman',
            'ulasan.user'
        ])->latest();

        // SEARCH
        if ($request->search) {
            $query->where(
                'Judul',
                'like',
                '%' . $request->search . '%'
            );
        }

        $buku = $query->paginate(10);

        foreach ($buku as $item) {

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

        $dipinjamBulanIni =
            \App\Models\Peminjaman::whereMonth(
                'TanggalPeminjaman',
                now()->month
            )->count();

        $telat =
            \App\Models\Peminjaman::where(
                'StatusPeminjaman',
                'Terlambat'
            )->count();

        return view(
            'admin.dashboard',
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

    public function users(Request $request)
    {
        $query = User::query()->latest();

        // SEARCH

        if ($request->search) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'Username',
                    'like',
                    '%' . $request->search . '%'
                )
                ->orWhere(
                    'NamaLengkap',
                    'like',
                    '%' . $request->search . '%'
                )
                ->orWhere(
                    'Email',
                    'like',
                    '%' . $request->search . '%'
                );

            });

        }

        $users = $query->paginate(10);

        // STATISTIK

        $totalAnggota = User::count();

        $anggotaAktif = User::where(
            'Status',
            'Aktif'
        )->count();

        $totalPeminjam = User::where(
            'Role',
            'Peminjam'
        )->count();

        $totalPetugas = User::where(
            'Role',
            'Petugas'
        )->count();

        $totalAdmin = User::where(
            'Role',
            'Admin'
        )->count();

        return view(
            'admin.users',
            compact(
                'users',
                'totalAnggota',
                'anggotaAktif',
                'totalPeminjam',
                'totalPetugas',
                'totalAdmin'
            )
        );
    }

    public function bookSuggestions(Request $request)
    {
        $search = $request->q;

        if (!$search) {
            return response()->json([]);
        }

        $books = Buku::where('Judul', 'like', "%{$search}%")
            ->orWhere('Penulis', 'like', "%{$search}%")
            ->orWhere('Penerbit', 'like', "%{$search}%")
            ->limit(6)
            ->get([
                'BukuID',
                'Judul',
                'Penulis',
                'Cover'
            ]);

        return response()->json($books);
    }

    public function userSuggestions(Request $request)
    {
        $search = $request->q;

        if (!$search) {
            return response()->json([]);
        }

        $users = User::where('Username', 'like', "%{$search}%")
            ->orWhere('NamaLengkap', 'like', "%{$search}%")
            ->orWhere('Email', 'like', "%{$search}%")
            ->limit(6)
            ->get([
                'UserID',
                'Username',
                'NamaLengkap',
                'Email',
                'Foto'
            ]);

        return response()->json($users);
    }

    public function showUser($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function updateUser(
        Request $request,
        $id
    )
    {
        $user = User::findOrFail($id);

        $request->validate([
            'Username' => [
                'required',
                Rule::unique('user', 'Username')
                    ->ignore($user->UserID, 'UserID')
            ],
            'NamaLengkap' => 'required',
            'Email' => [
                'required',
                'email',
                Rule::unique('user', 'Email')
                    ->ignore($user->UserID, 'UserID')
            ],
            'Role' => 'required',
            'Alamat' => 'nullable|string',
        ]);

        $user->update([
            'Username' => $request->Username,
            'NamaLengkap' => $request->NamaLengkap,
            'Email' => $request->Email,
            'Role' => $request->Role,
            'Alamat' => $request->Alamat,
        ]);

        AuditHelper::log(
            'Edit User',
            'Mengubah data user ' .
            $user->Username
        );

        return response()->json([
            'success' => true
        ]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'Username'     => 'required|string|max:255|unique:user,Username',
            'NamaLengkap'  => 'required|string|max:255',
            'Email'        => 'required|email|max:255|unique:user,Email',
            'Password'     => 'required|string|min:8|max:255',
            'Alamat'       => 'nullable|string',
            'Role'         => 'required|in:Admin,Petugas,Peminjam',
        ]);

        $fotoName = null;

        if ($request->hasFile('Foto')) {

            $file = $request->file('Foto');

            $fotoName =
                Str::uuid() .
                '.' .
                $file->getClientOriginalExtension();

            $file->storeAs(
                'user',
                $fotoName,
                'public'
            );
        }

        $newUser = User::create([
            'Username' => $request->Username,
            'NamaLengkap' => $request->NamaLengkap,
            'Email' => $request->Email,
            'Password' => Hash::make(
                $request->Password
            ),
            'Role' => $request->Role,
            'Status' => 'Aktif',
            'Foto' => $fotoName,
            'Alamat' => $request->Alamat
        ]);

        AuditHelper::log(
            'Tambah User',
            'Menambahkan user ' .
            $newUser->Username .
            ' dengan role ' .
            $newUser->Role
        );

        return response()->json([
            'success' => true
        ]);
    }

    public function checkUser(Request $request)
    {
        $username = $request->username;
        $email = $request->email;
        $userId = $request->user_id;

        $usernameExists = false;
        $emailExists = false;

        if ($username) {
            $usernameExists = User::where('Username', $username)
                ->where('UserID', '!=', $userId)
                ->exists();
        }

        if ($email) {
            $emailExists = User::where('Email', $email)
                ->where('UserID', '!=', $userId)
                ->exists();
        }

        return response()->json([
            'usernameExists' => $usernameExists,
            'emailExists' => $emailExists,
        ]);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->UserID) {

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menonaktifkan akun sendiri'
            ], 403);

        }

        // admin terakhir tidak boleh dinonaktifkan
        if (
            $user->Role === 'Admin'
            && $user->Status === 'Aktif'
        ) {

            $adminAktif = User::where(
                'Role',
                'Admin'
            )
            ->where(
                'Status',
                'Aktif'
            )
            ->count();

            if ($adminAktif <= 1) {

                return response()->json([
                    'success' => false,
                    'message' => 'Admin terakhir tidak dapat dinonaktifkan'
                ], 422);

            }
        }

        $user->Status =
            $user->Status === 'Aktif'
            ? 'Nonaktif'
            : 'Aktif';

        $user->save();

        AuditHelper::log(
            $user->Status === 'Aktif'
                ? 'Aktifkan User'
                : 'Nonaktifkan User',

            'Mengubah status user ' .
            $user->Username .
            ' menjadi ' .
            $user->Status
        );

        return response()->json([
            'success' => true,
            'status' => $user->Status
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'Judul' => 'required',
                'Penulis' => 'required',
                'Penerbit' => 'required',
                'TahunTerbit' => 'required',
                'Deskripsi' => 'required',
                'Stok' => 'required|integer|min:1',
                'KategoriID' => 'required|array|min:1',
                'KategoriID.*' => 'exists:kategoribuku,KategoriID',
                'Cover' => 'required|file|mimes:jpg,jpeg,png,webp,avif|max:2048'
            ],
            [
                'Judul.required' => 'Judul buku wajib diisi.',
                'Penulis.required' => 'Penulis wajib diisi.',
                'Penerbit.required' => 'Penerbit wajib diisi.',
                'TahunTerbit.required' => 'Tahun terbit wajib diisi.',
                'Deskripsi.required' => 'Deskripsi wajib diisi.',
                'Stok.required' => 'Stok wajib diisi.',
                'Stok.integer' => 'Stok harus berupa angka.',
                'Stok.min' => 'Stok minimal 1.',
                'KategoriID.required' => 'Pilih minimal satu kategori.',
                'Cover.required' => 'Cover buku wajib diunggah.',
                'Cover.mimes' => 'Cover harus berformat JPG, PNG, WEBP, atau AVIF.',
                'Cover.max' => 'Ukuran cover maksimal 2 MB.'
            ]
        );

        // upload cover
        $coverName =
            Str::uuid() .
            '.' .
            $request->Cover->extension();

        $request->Cover->storeAs(
            'books',
            $coverName,
            'public'
        );

        // simpan buku
        $buku = Buku::create([
            'Judul' => $request->Judul,
            'Penulis' => $request->Penulis,
            'Penerbit' => $request->Penerbit,
            'TahunTerbit' => $request->TahunTerbit,
            'Deskripsi' => $request->Deskripsi,
            'Stok' => $request->Stok,
            'Cover' => $coverName,
        ]);

        // simpan kategori relasi
        foreach ($request->KategoriID as $kategoriId) {

            \App\Models\KategoriBukuRelasi::create([
                'BukuID' => $buku->BukuID,
                'KategoriID' => $kategoriId
            ]);

        }

        AuditHelper::log(
            'Tambah Buku',
            'Menambahkan buku "' .
            $buku->Judul .
            '"'
        );

        $buku->load('kategoriRelasi.kategori');

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data' => [
                'BukuID' => $buku->BukuID,
                'Judul' => $buku->Judul,
                'Penulis' => $buku->Penulis,
                'Penerbit' => $buku->Penerbit,
                'TahunTerbit' => $buku->TahunTerbit,
                'Deskripsi' => $buku->Deskripsi,
                'Stok' => $buku->Stok,
                'Cover' => $buku->Cover,
            ]
        ]);
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);

        $sedangDipinjam = $buku->peminjaman()
            ->where('StatusPeminjaman', 'Dipinjam')
            ->exists();

        if ($sedangDipinjam) {

            return response()->json([
                'success' => false,
                'message' => 'Buku sedang dipinjam dan tidak dapat dihapus'
            ], 422);

        }

        // hapus cover jika ada
        if (
            $buku->Cover &&
            file_exists(
                storage_path(
                    'app/public/books/' . $buku->Cover
                )
            )
        ) {
            unlink(
                storage_path(
                    'app/public/books/' . $buku->Cover
                )
            );
        }

        AuditHelper::log(
            'Hapus Buku',
            'Menghapus buku "' .
            $buku->Judul .
            '"'
        );

        $buku->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function activity()
    {
        $logs = AuditLog::with('user')
            ->latest('CreatedAt')
            ->paginate(20);

        $totalLog = AuditLog::count();

        $roleChanges = AuditLog::where(
            'Aksi',
            'like',
            '%Role%'
        )->count();

        $bookChanges = AuditLog::where(
            'Aksi',
            'like',
            '%Buku%'
        )->count();

        $statusChanges = AuditLog::where(
            'Aksi',
            'like',
            '%Status%'
        )->count();

        return view(
            'admin.activity',
            compact(
                'logs',
                'totalLog',
                'roleChanges',
                'bookChanges',
                'statusChanges'
            )
        );
    }
}
