<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth', ['mode' => 'login']);
    }

    public function showRegister(): View
    {
        return view('auth', ['mode' => 'register']);
    }

    public function showRegistration(string $role): View
    {
        $role = $this->normalizeRole($role);

        return view('auth', [
            'mode' => 'register',
            'selectedRole' => $role,
            'roleLabel' => $this->roleLabel($role),
        ]);
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:siswa,petugas_pmr,wali_kelas,admin'],
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'role.required' => 'Peran wajib dipilih.',
            'identifier.required' => 'Identitas wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = $this->findUserByIdentifier($validated['identifier'], $validated['role']);

        if (! $user || ! Hash::check($validated['password'], $user->password) || $user->role !== $validated['role']) {
            return back()
                ->withErrors(['identifier' => 'Identitas atau password salah.'])
                ->withInput();
        }

        if ($user->role === 'wali_kelas' && DB::table('wali_kelas')
            ->where('id_user', $user->id)
            ->where('status', 'Nonaktif')
            ->exists()) {
            return back()
                ->withErrors(['identifier' => 'Akun wali kelas ini sedang nonaktif.'])
                ->withInput();
        }

        if ($user->role === 'petugas_pmr' && DB::table('petugas_pmr')
            ->where('id_user', $user->id)
            ->where('status', 'Nonaktif')
            ->exists()) {
            return back()
                ->withErrors(['identifier' => 'Akun petugas PMR ini sedang nonaktif.'])
                ->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request, string $role): RedirectResponse
    {
        $role = $this->normalizeRole($role);

        if (in_array($role, ['petugas_pmr', 'wali_kelas'], true)) {
            abort(403, 'Akun Petugas PMR dan Wali Kelas hanya dapat dibuat melalui menu pengelolaan oleh admin.');
        }

        if ($role === 'admin' && (! Auth::check() || Auth::user()->role !== 'admin')) {
            abort(403, 'Akun admin hanya dapat dibuat oleh admin yang sudah login.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];

        if ($role === 'siswa') {
            $rules['nisn'] = ['required', 'string', 'max:20', 'unique:siswa,nisn'];
            $rules['kelas'] = ['required', 'string', 'max:100'];
            $rules['jenis_kelamin'] = ['required', 'string', 'in:Laki-laki,Perempuan'];

            $messages['nisn.required'] = 'NISN wajib diisi.';
            $messages['nisn.unique'] = 'NISN ini sudah terdaftar.';
            $messages['kelas.required'] = 'Kelas wajib diisi.';
            $messages['jenis_kelamin.required'] = 'Jenis kelamin wajib dipilih.';
        }

        if ($role === 'wali_kelas') {
            $rules['kelas_dampu'] = ['required', 'string', 'max:100'];
            $messages['kelas_dampu.required'] = 'Kelas dampu wajib diisi.';
        }

        $validated = $request->validate($rules, $messages);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $role,
            'password' => $validated['password'],
        ]);

        if ($role === 'siswa') {
            DB::table('siswa')->insert([
                'id_user' => $user->id,
                'nisn' => $validated['nisn'],
                'kelas' => $validated['kelas'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($role === 'wali_kelas') {
            DB::table('wali_kelas')->insert([
                'id_user' => $user->id,
                'kelas_dampu' => $validated['kelas_dampu'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($role === 'petugas_pmr') {
            DB::table('petugas_pmr')->insert([
                'id_user' => $user->id,
                'status_piket' => 'Belum dijadwalkan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function normalizeRole(string $role): string
    {
        return in_array($role, ['siswa', 'petugas_pmr', 'wali_kelas', 'admin'], true)
            ? $role
            : abort(404, 'Peran tidak ditemukan.');
    }

    private function roleLabel(string $role): string
    {
        return match ($role) {
            'siswa' => 'Siswa',
            'petugas_pmr' => 'Petugas PMR',
            'wali_kelas' => 'Wali Kelas',
            'admin' => 'Admin',
            default => 'Pengguna',
        };
    }

    private function findUserByIdentifier(string $identifier, string $role): ?User
    {
        if ($role === 'siswa') {
            $student = DB::table('siswa')->where('nisn', $identifier)->first();

            if (! $student) {
                return null;
            }

            return User::find($student->id_user);
        }

        return User::where('email', $identifier)->first();
    }
}
