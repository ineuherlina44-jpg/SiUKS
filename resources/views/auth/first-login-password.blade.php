<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ganti Password | SiUKS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body style="min-height: 100vh; display: grid; place-items: center; margin: 0; padding: 20px; background: #eaf3fc; font-family: Inter, Arial, sans-serif; color: #17324d;">
    <main style="width: min(100%, 440px); box-sizing: border-box; padding: 28px; border-radius: 16px; background: #fff; box-shadow: 0 16px 48px rgba(23, 50, 77, 0.12);">
        <p style="margin: 0 0 8px; color: #2e9b67; font-weight: 700;">SiUKS · Keamanan akun</p>
        <h1 style="margin: 0 0 12px; font-size: 24px;">Buat password baru</h1>
        <p style="margin: 0 0 20px; line-height: 1.6;">Password sementara hanya berlaku untuk login pertama. Buat password pribadi minimal 12 karakter agar dapat melanjutkan.</p>

        @if ($errors->any())
            <div role="alert" style="margin-bottom: 16px; color: #b42318;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}">
            @csrf
            @method('PUT')
            <label for="current_password" style="display: block; margin-bottom: 6px; font-weight: 600;">Password sementara</label>
            <input id="current_password" name="current_password" type="password" required autocomplete="current-password" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            @error('current_password')<small role="alert" style="color: #b42318;">{{ $message }}</small>@enderror

            <label for="password" style="display: block; margin-bottom: 6px; font-weight: 600;">Password baru</label>
            <input id="password" name="password" type="password" minlength="12" required autocomplete="new-password" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
            @error('password')<small role="alert" style="color: #b42318;">{{ $message }}</small>@enderror

            <label for="password_confirmation" style="display: block; margin: 16px 0 6px; font-weight: 600;">Konfirmasi password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" minlength="12" required autocomplete="new-password" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">

            <button type="submit" style="width: 100%; margin-top: 20px; padding: 12px; border: 0; border-radius: 8px; background: #2e9b67; color: #fff; font-weight: 700; cursor: pointer;">Simpan password baru</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top: 12px; text-align: center;">
            @csrf
            <button type="submit" style="border: 0; background: transparent; color: #475569; text-decoration: underline; cursor: pointer;">Keluar</button>
        </form>
    </main>
</body>
</html>
