<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user  = Auth::user();
        $stats = ['user_name' => $user->name]; // optional untuk hero
        return view('Auth.profile', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required','string','min:3','max:100'],
            'email' => ['required','email','max:150', Rule::unique('users','email')->ignore($user->id)],
            'current_password' => ['nullable','string','min:6'],
            'password' => ['nullable','string','min:6','confirmed'],
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            if ($request->filled('current_password') && ! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
