<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'Username'      => ['required', 'string', 'max:255', 'unique:user,Username'],
            'Email'         => ['required', 'email', 'max:255', 'unique:user,Email'],
            'NamaLengkap'   => ['required', 'string', 'max:255'],
            'Alamat'        => ['required', 'string'],
            'Password'      => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = strtolower(trim($request->Email));
        $username = trim($request->Username);

        $user = User::create([
            'Username'      => $username,
            'Email'         => $email,
            'NamaLengkap'   => $request->NamaLengkap,
            'Alamat'        => $request->Alamat,
            'Password'      => Hash::make($request->Password),
            'Role'          => 'Peminjam',
            'Status'        => 'Aktif',
            'Foto'          => null
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/dashboard');
    }
}
