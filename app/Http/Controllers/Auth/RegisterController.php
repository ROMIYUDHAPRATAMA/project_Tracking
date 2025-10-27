<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /** tampilkan halaman register */
    public function show()
    {
        return view('auth.register');
    }

    /** simpan data user baru */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        // password otomatis di-hash (lihat User model casts)
        User::create($validated);

        return redirect()->route('login')
            ->with('success','Registrasi berhasil! Silakan login.');
    }
}
