<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\UlasanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/landing', function () {
    return view('landing');
})->name('landing');

Route::get('/books', [DashboardController::class, 'guest'])
    ->name('books');


/*
|--------------------------------------------------------------------------
| PEMINJAM
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Peminjam'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Koleksi
    Route::post('/koleksi/toggle', [KoleksiController::class, 'toggle']);
    Route::post('/koleksi/delete-selected', [KoleksiController::class, 'deleteSelected']);

    // Ulasan
    Route::post('/ulasan/store', [UlasanController::class, 'store']);

    // Peminjaman
    Route::post('/peminjaman/store', [PeminjamanController::class, 'store']);

    // Pengembalian
    Route::post('/pengembalian/{id}', [PeminjamanController::class, 'pengembalian']);
});


/*
|--------------------------------------------------------------------------
| PETUGAS
|--------------------------------------------------------------------------
*/

Route::prefix('petugas')
    ->middleware(['auth', 'role:Petugas'])
    ->group(function () {

        Route::get('/dashboard', [PetugasController::class, 'dashboard'])
            ->name('petugas.dashboard');

        /*
         * Tambahkan fitur petugas di sini nanti:
         *
         * Route::get('/peminjaman', ...);
         * Route::post('/verifikasi', ...);
         * Route::get('/laporan', ...);
         *
         */
    });


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'role:Admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get(
            '/activity',
            [AdminController::class, 'activity']
        )->name('admin.activity');

        /*
        |--------------------------------------------------------------------------
        | KELOLA USER
        |--------------------------------------------------------------------------
        */

        Route::get('/users', [AdminController::class, 'users'])
            ->name('admin.users');

        Route::post('/users/store', [AdminController::class, 'storeUser']);

        Route::patch('/users/{id}/role', [UserController::class, 'changeRole']);

        Route::get('/users/suggestions', [AdminController::class, 'userSuggestions'])
            ->name('admin.users.suggestions');

        Route::get('/check-user', [AdminController::class, 'checkUser']);

        Route::get('/users/{id}', [AdminController::class, 'showUser']);

        Route::put('/users/{id}', [AdminController::class, 'updateUser']);

        Route::patch('/users/{id}/status', [AdminController::class, 'toggleStatus']);

        Route::post('/users/{id}/photo', [UserController::class, 'updatePhoto']);

        /*
        |--------------------------------------------------------------------------
        | BUKU
        |--------------------------------------------------------------------------
        */

        Route::post('/books', [AdminController::class, 'store']);

        Route::delete('/books/{id}', [AdminController::class, 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | LAPORAN
        |--------------------------------------------------------------------------
        */

        Route::get('/laporan/preview', [LaporanController::class, 'preview']);
    });


/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';