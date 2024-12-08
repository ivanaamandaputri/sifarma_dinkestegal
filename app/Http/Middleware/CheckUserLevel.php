<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$levels
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$levels)
    {
        // Memastikan pengguna sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Mengambil level user yang sedang login
        $userLevel = Auth::user()->level;

        // Cek apakah level user termasuk dalam array level yang diperbolehkan
        if (!in_array($userLevel, $levels)) {
            return abort(403, 'Unauthorized action. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
