<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi SiUKS</title>
    <style>
        :root { font-family: Inter, "Segoe UI", sans-serif; color: #17324D; background: #FFFFFF; }
        * { box-sizing: border-box; }
        body { min-width: 320px; min-height: 100vh; margin: 0; padding: 0 20px 36px; color: #17324D; background: #FFFFFF; }
        .topbar { display: flex; min-height: 68px; align-items: center; justify-content: space-between; gap: 14px; padding: 10px max(20px, calc((100vw - 980px) / 2)); border-bottom: 1px solid #F0D1D5; background: #FFFFFF; }
        .brand { display: flex; align-items: center; gap: 10px; color: #D62839; font-size: 15px; font-weight: 700; }
        .brand img { width: 42px; height: 42px; object-fit: contain; }
        .brand small { display: block; margin-top: 2px; color: #17324D; font-size: 10px; font-weight: 400; }
        main { width: min(900px, 100%); margin: 32px auto 0; padding: 28px; border: 1px solid #E5EAF0; border-radius: 8px; background: #FFFFFF; }
        .eyebrow { margin: 0 0 7px; color: #D62839; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        h1 { margin: 0; color: #17324D; font-size: 25px; }.intro { margin: 8px 0 22px; color: #52677A; font-size: 12px; line-height: 1.5; }
        .roles { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .role-link { display: grid; min-height: 94px; align-content: center; gap: 7px; padding: 16px; color: #17324D; border: 1px solid #D7E4F2; border-radius: 6px; background: #EAF3FF; text-decoration: none; }
        .role-link:hover { border-color: #D62839; background: #FDE7EA; }.role-link strong { font-size: 14px; }.role-link span { color: #52677A; font-size: 11px; }
        form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .field { display: grid; gap: 5px; }.field.wide { grid-column: 1 / -1; }
        label { color: #17324D; font-size: 11px; font-weight: 600; }
        input, select { width: 100%; height: 41px; padding: 0 11px; color: #17324D; border: 1px solid #CAD8E6; border-radius: 5px; outline: none; background: #FFFFFF; font: inherit; font-size: 12px; }
        input:focus, select:focus { border-color: #D62839; box-shadow: 0 0 0 3px #D6283918; }
        .password-control { position: relative; }
        .password-control input { padding-right: 44px; }
        .password-toggle { position: absolute; top: 0; right: 0; display: grid; width: 41px; height: 41px; min-height: 41px; place-items: center; margin: 0; padding: 0; color: #52677A; border: 0; border-radius: 4px; background: transparent; cursor: pointer; }
        .password-toggle:hover { color: #D62839; background: transparent; }
        .password-toggle:focus-visible { outline: 2px solid #D62839; outline-offset: -3px; }
        .password-toggle svg { width: 18px; height: 18px; }
        .password-toggle .eye-slash { display: none; }
        .password-toggle[aria-pressed="true"] .eye-open { display: none; }
        .password-toggle[aria-pressed="true"] .eye-slash { display: block; }
        button { grid-column: 1 / -1; min-height: 43px; margin-top: 4px; color: #FFFFFF; border: 0; border-radius: 5px; background: #D62839; cursor: pointer; font: inherit; font-size: 12px; font-weight: 700; }
        button:hover { background: #B71F30; }.error { color: #D62839; font-size: 10px; }
        .notice, .warning { padding: 10px; border-radius: 5px; font-size: 11px; }.notice { color: #236F4D; border: 1px solid #B9E1CC; background: #EAF6EF; }.warning { color: #805018; border: 1px solid #F4A340; background: #FFF5E8; }
        .back { display: inline-block; margin-top: 18px; color: #D62839; font-size: 11px; font-weight: 600; text-decoration: none; }
        @media (max-width: 600px) { body { padding: 0 12px 24px; }.topbar { padding: 8px 12px; }.brand img { width: 36px; height: 36px; } main { margin-top: 18px; padding: 20px 16px; }.roles, form { grid-template-columns: 1fr; }.field.wide, button { grid-column: auto; } }
    </style>
</head>
<body>
    <header class="topbar"><div class="brand"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""><span>SiUKS<small>Sistem Informasi Kesehatan Sekolah</small></span></div></header>
    <main>
        @if (session('status'))<p class="notice">{{ session('status') }}</p>@endif

        @if ($role === null)
            <p class="eyebrow">Pilih kategori akun</p>
            <h1>Registrasi</h1>
            <p class="intro">Pilih peran sesuai tugas di sekolah.</p>
            <div class="roles">
                <a class="role-link" href="{{ route('register.role', ['role' => 'siswa']) }}"><strong>Siswa</strong><span>Daftar dengan NISN dan data kelas.</span></a>
                <a class="role-link" href="{{ route('register.role', ['role' => 'petugas_pmr']) }}"><strong>Petugas PMR</strong><span>Daftar menggunakan email.</span></a>
                <a class="role-link" href="{{ route('register.role', ['role' => 'wali_kelas']) }}"><strong>Wali Kelas</strong><span>Daftar dengan email dan kelas dampu.</span></a>
                @if (auth()->check() && auth()->user()->role === 'admin')
                    <a class="role-link" href="{{ route('register.role', ['role' => 'admin']) }}"><strong>Admin / Pembina</strong><span>Buat akun Admin baru.</span></a>
                @else
                    <div class="role-link"><strong>Admin / Pembina</strong><span>Akun dibuat oleh Admin yang sudah masuk.</span></div>
                @endif
            </div>
        @else
            <p class="eyebrow">{{ $roleLabel }}</p>
            <h1>Daftar {{ $roleLabel }}</h1>
            <p class="intro">Lengkapi data untuk membuat akun.</p>
            <form method="POST" action="{{ route('register.store', ['role' => $role]) }}">
                @csrf
                <div class="field"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<span class="error">{{ $message }}</span>@enderror</div>

                @if ($role === 'siswa')
                    <div class="field"><label for="nisn">NISN</label><input id="nisn" name="nisn" value="{{ old('nisn') }}" inputmode="numeric" required>@error('nisn')<span class="error">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="kelas">Kelas</label><input id="kelas" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: XI IPA 1" required>@error('kelas')<span class="error">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="jenis_kelamin">Jenis kelamin</label><select id="jenis_kelamin" name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select>@error('jenis_kelamin')<span class="error">{{ $message }}</span>@enderror</div>
                @elseif ($role === 'wali_kelas')
                    <div class="field"><label for="kelas_dampu">Kelas dampu</label><input id="kelas_dampu" name="kelas_dampu" value="{{ old('kelas_dampu') }}" placeholder="Contoh: XI IPA 1" required>@error('kelas_dampu')<span class="error">{{ $message }}</span>@enderror</div>
                @elseif ($role === 'petugas_pmr')
                    <p class="intro field wide">Status piket awal akan diatur sebagai Belum dijadwalkan.</p>
                @elseif ($role === 'admin')
                    <p class="warning field wide">Pendaftaran Admin hanya dapat dilakukan oleh Admin yang sudah login.</p>
                @endif

                <div class="field"><label for="password">Password</label><span class="password-control"><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required><button class="password-toggle" type="button" data-password-toggle="password" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path class="eye-open" d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle class="eye-open" cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/><path class="eye-slash" d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.1 0 9.5 6 9.5 6a14.2 14.2 0 0 1-3.1 3.6M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.4 6 9.5 6c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span>@error('password')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="password_confirmation">Konfirmasi password</label><span class="password-control"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required><button class="password-toggle" type="button" data-password-toggle="password_confirmation" aria-label="Tampilkan konfirmasi password" aria-pressed="false" title="Tampilkan konfirmasi password"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path class="eye-open" d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle class="eye-open" cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/><path class="eye-slash" d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.1 0 9.5 6 9.5 6a14.2 14.2 0 0 1-3.1 3.6M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.4 6 9.5 6c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span></div>
                <button type="submit">Daftar sebagai {{ $roleLabel }}</button>
            </form>
        @endif

        <a class="back" href="{{ route('login') }}">Kembali ke login</a>
    </main>
</body>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        const input = document.getElementById(toggle.dataset.passwordToggle);

        toggle.addEventListener('click', () => {
            const showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(showPassword));
            toggle.setAttribute('aria-label', showPassword ? 'Sembunyikan password' : 'Tampilkan password');
            toggle.title = showPassword ? 'Sembunyikan password' : 'Tampilkan password';
        });
    });
</script>
</html>
