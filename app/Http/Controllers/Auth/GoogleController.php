<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        // TODO: isi dengan Socialite/SDK milikmu
        return back()->with('success', 'Google login belum diaktifkan.');
    }

    public function handleGoogleCallback(Request $request)
    {
        // TODO: proses callback Google
        return redirect()->route('login')->with('success', 'Google login belum diaktifkan.');
    }
}
