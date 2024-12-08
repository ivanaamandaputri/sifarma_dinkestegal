<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Coba untuk login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Regenerate session untuk keamanan

            // Redirect ke dashboard yang sesuai berdasarkan level pengguna
            if (Auth::user()->level == 'admin') {
                return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            } elseif (Auth::user()->level == 'operator') {
                return redirect()->route('dashboard.operator')->with('success', 'Login berhasil!');
            }
        }

        // Jika login gagal
        return back()->withErrors([
            'nip' => 'NIP atau password salah.',
        ])->onlyInput('nip');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}
