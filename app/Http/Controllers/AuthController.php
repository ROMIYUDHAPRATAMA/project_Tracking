<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct()
    {
        // Halaman/form auth untuk guest
        $this->middleware('guest')->only([
            'loginPage', 'registerPage', 'login', 'register',
            // <- JANGAN masukkan method OAuth di sini agar tidak bentrok
        ]);

        $this->middleware('auth')->only(['logout']);
    }

    /** GET /login */
    public function loginPage()
    {
        return view('auth.login');
    }

    /** POST /login */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            // intended agar kalau ada halaman tujuan sebelumnya tetap diikuti
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    /** GET /register */
    public function registerPage()
    {
        return view('auth.register');
    }

    /** POST /register */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /** POST /logout */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ==========================
    // Google OAuth (Socialite)
    // ==========================

    /** GET /login/google */
    public function redirectToGoogle()
    {
        // stateless menghindari "Invalid state" saat dev
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /** GET /login/google/callback */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $email = $googleUser->getEmail();
            $gid   = $googleUser->getId();

            if (!$email) {
                return redirect()->route('login')->with('error', 'Login Google gagal: email tidak tersedia pada akun Google.');
            }

            Log::info('Google OAuth callback', ['email' => $email, 'gid' => $gid]);

            // Transaksi aman: insert jika baru, update google_id jika sudah ada
            $user = DB::transaction(function () use ($email, $gid, $googleUser) {
                return User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name'      => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Pengguna',
                        'google_id' => $gid,
                        // cast 'password' => 'hashed' akan otomatis hash string berikut
                        'password'  => Str::password(40),
                    ]
                );
            });

            Auth::login($user, remember: true);
            session()->regenerate(); // penting agar sesi nempel

            return redirect()->intended(route('dashboard'));
        } catch (Exception $e) {
            Log::error('Google OAuth error', ['message' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }
    }
}
