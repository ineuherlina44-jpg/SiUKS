<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FirstLoginPasswordController extends Controller
{
    public function edit(): View
    {
        abort_unless(Auth::user()?->password_change_required, 404);

        return view('auth.first-login-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->password_change_required, 403);

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:12', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Password sementara wajib diisi.',
            'current_password.current_password' => 'Password sementara tidak cocok.',
            'password.min' => 'Password baru minimal 12 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.different' => 'Password baru harus berbeda dari password sementara.',
        ]);

        $user->forceFill([
            'password' => $validated['password'],
            'password_change_required' => false,
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Password berhasil diperbarui.');
    }
}
