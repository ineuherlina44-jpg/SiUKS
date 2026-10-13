<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Memeriksa hak akses multi-role (Siswa, Petugas PMR, Wali Kelas, Admin).
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        // 1. Cek apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek apakah role pengguna ada di dalam daftar role yang diizinkan
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
