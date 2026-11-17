<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FDE7EA">
    <title>SiUKS | Masuk & Daftar</title>
    <style>
        :root {
            --page: #f5f7fb;
            --panel: #ffffff;
            --panel-soft: #EAF3FF;
            --line: #dfeaf3;
            --text: #17324d;
            --muted: #587085;
            --primary: #d62839;
            --primary-dark: #b91d2f;
            --primary-soft: #FDE7EA;
            --accent: #EAF3FF;
            --success: #2E9B67;
            --warning: #F4A340;
            --shadow: 0 22px 48px rgba(24, 51, 77, 0.13);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body {
            min-width: 320px;
            background: linear-gradient(135deg, #edf4ff 0%, #f7f9fc 44%, #fff6f7 100%);
            font-family: Inter, "Segoe UI", sans-serif;
            color: var(--text);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select { font: inherit; }

        .auth-shell {
            display: grid;
            min-height: 100vh;
            place-items: center;
            padding: 28px 18px;
        }

        .auth-layout {
            display: grid;
            width: min(1100px, 100%);
            min-height: 680px;
            grid-template-columns: 1.05fr 1.15fr;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .auth-visual {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 22px;
            padding: 34px 30px;
            background: linear-gradient(180deg, #17324d 0%, #0f2944 100%);
            color: #eff7ff;
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            width: fit-content;
        }

        .auth-brand-mark {
            display: grid;
            width: 46px;
            height: 46px;
            place-items: center;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(255,255,255,0.14);
        }

        .auth-brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .auth-brand-copy {
            display: grid;
            gap: 2px;
            line-height: 1.2;
        }

        .auth-brand-copy strong {
            font-size: 18px;
            letter-spacing: 0.2px;
        }

        .auth-brand-copy span {
            font-size: 11px;
            color: rgba(232, 241, 251, 0.72);
        }

        .auth-visual-copy {
            display: grid;
            gap: 14px;
            max-width: 430px;
        }

        .auth-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #e4f0ff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .auth-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ffbaae;
            box-shadow: 0 0 0 6px rgba(255, 186, 174, 0.14);
        }

        .auth-visual-copy h1 {
            margin: 0;
            font-size: clamp(30px, 3vw, 44px);
            line-height: 1.08;
            letter-spacing: -0.04em;
        }

        .auth-visual-copy p {
            margin: 0;
            color: rgba(232, 241, 251, 0.78);
            line-height: 1.7;
            font-size: 14px;
        }

        .auth-feature-list {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .auth-feature-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            color: #edf5ff;
            font-size: 13px;
        }

        .auth-feature-list li::before {
            content: "✓";
            display: inline-grid;
            place-items: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(214, 40, 57, 0.16);
            color: #ffd9dc;
            font-size: 12px;
            font-weight: 800;
        }

        .auth-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 34px clamp(20px, 4vw, 48px);
            background: var(--panel);
        }

        .auth-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .auth-topbar h2 {
            margin: 0;
            font-size: clamp(24px, 2vw, 32px);
            letter-spacing: -0.04em;
        }

        .auth-tabs {
            display: inline-flex;
            align-items: center;
            padding: 5px;
            background: #f0f5fa;
            border: 1px solid var(--line);
            border-radius: 999px;
        }

        .auth-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            min-height: 38px;
            padding: 0 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            transition: all 0.16s ease;
        }

        .auth-tab.active {
            color: #fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 0 8px 18px rgba(214, 40, 57, 0.25);
        }

        .auth-form {
            display: grid;
            gap: 18px;
        }

        .auth-form-header {
            display: grid;
            gap: 5px;
        }

        .auth-form-header h3 {
            margin: 0;
            font-size: 19px;
        }

        .auth-form-header p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .role-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 62px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--panel-soft);
            color: var(--text);
            cursor: pointer;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, transform 0.16s ease;
        }

        .role-option:hover {
            border-color: rgba(214, 40, 57, 0.35);
            transform: translateY(-1px);
        }

        .role-option.is-selected {
            border-color: rgba(214, 40, 57, 0.7);
            background: var(--primary-soft);
            box-shadow: 0 8px 18px rgba(214, 40, 57, 0.08);
        }

        .role-option strong {
            display: block;
            font-size: 14px;
        }

        .role-option small {
            display: block;
            color: var(--muted);
            font-size: 11px;
        }

        .role-badge {
            display: inline-grid;
            place-items: center;
            min-width: 30px;
            min-height: 30px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid rgba(214, 40, 57, 0.12);
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .field label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
        }

        .field input,
        .field select {
            width: 100%;
            min-height: 46px;
            padding: 0 13px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--text);
            outline: none;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
        }

        .field input:focus,
        .field select:focus {
            border-color: rgba(214, 40, 57, 0.7);
            box-shadow: 0 0 0 3px rgba(214, 40, 57, 0.1);
        }

        .field input:hover,
        .field select:hover { border-color: #c7d5e3; }

        .field input::placeholder { color: #8ca0b5; }

        .field input:disabled,
        .field select:disabled { cursor: not-allowed; background: #f5f7fa; }

        .field input[aria-invalid="true"],
        .field select[aria-invalid="true"] { border-color: var(--primary); }

        .field-error-text { color: var(--primary); font-size: 11px; font-weight: 600; }

        .field-error {
            min-height: 16px;
            color: var(--primary);
            font-size: 11px;
            font-weight: 600;
        }

        .helper-box {
            display: grid;
            gap: 8px;
            padding: 12px 14px;
            border: 1px solid #d9ebdd;
            border-radius: 12px;
            background: #effcf4;
            color: #1a7749;
            font-size: 12px;
            line-height: 1.6;
        }

        .helper-box.admin {
            border-color: #f2d5c8;
            background: #fff4ee;
            color: #a24d27;
        }

        .switch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            color: var(--muted);
            font-size: 12px;
        }

        .switch-row label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .switch-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
        }

        .auth-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 50px;
            width: 100%;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: transform 0.14s ease, box-shadow 0.14s ease;
        }

        .auth-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(214, 40, 57, 0.22);
        }

        .auth-submit:disabled { opacity: .65; cursor: not-allowed; transform: none; box-shadow: none; }
        .auth-submit:focus-visible, .role-option:focus-visible, .auth-tab:focus-visible { outline: 3px solid rgba(214, 40, 57, .28); outline-offset: 2px; }

        .password-control { position: relative; display: block; }
        .password-control input { padding-right: 48px; }
        .password-toggle { position: absolute; top: 4px; right: 4px; display: grid; width: 38px; height: 38px; place-items: center; padding: 0; color: var(--muted); background: transparent; border: 0; border-radius: 8px; cursor: pointer; }
        .password-toggle:hover { color: var(--primary); background: var(--primary-soft); }
        .password-toggle:focus-visible { outline: 2px solid var(--primary); outline-offset: -2px; }
        .password-toggle svg { width: 19px; height: 19px; }
        .password-toggle .eye-slash { display: none; }
        .password-toggle[aria-pressed="true"] .eye-open { display: none; }
        .password-toggle[aria-pressed="true"] .eye-slash { display: block; }

        .auth-subtle {
            display: flex;
            justify-content: center;
            gap: 6px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }

        .auth-subtle a {
            color: var(--primary);
            font-weight: 700;
        }

        .role-section {
            display: none;
        }

        .role-section.active {
            display: grid;
            gap: 14px;
        }

        .field-hint { margin: -3px 0 0; color: var(--muted); font-size: 11px; line-height: 1.5; }

        @media (max-width: 820px) {
            .auth-layout {
                grid-template-columns: 1fr;
            }

            .auth-visual {
                min-height: 280px;
                padding: 24px 22px;
            }

            .auth-card {
                padding: 26px 18px 28px;
            }
        }

        @media (max-width: 540px) {
            .field-grid,
            .role-grid {
                grid-template-columns: 1fr;
            }

            .auth-topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .auth-tabs {
                width: 100%;
            }

            .auth-tab {
                flex: 1 1 0;
            }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="auth-layout">
            <aside class="auth-visual">
                <div class="auth-brand">
                    <div class="auth-brand-mark"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""></div>
                    <div class="auth-brand-copy">
                        <strong>SiUKS</strong>
                        <span>Sistem Informasi Kesehatan Sekolah</span>
                    </div>
                </div>

                <div class="auth-visual-copy">
                    <span class="auth-kicker">Dukungan UKS sekolah</span>
                    <h1>Kelola kesehatan sekolah dalam satu platform.</h1>
                    <p>SiUKS membantu siswa, petugas PMR, wali kelas, dan admin mengelola data kesehatan, pelaporan, dan koordinasi secara lebih teratur.</p>
                </div>

                <ul class="auth-feature-list">
                    <li>Data siswa dan pelayanan UKS lebih terpusat</li>
                    <li>Koordinasi PMR dan wali kelas lebih cepat</li>
                    <li>Peran pengguna disesuaikan dengan kebutuhan masing-masing</li>
                </ul>
            </aside>

            <section class="auth-card">
                <div class="auth-topbar">
                    <h2>{{ $mode === 'register' ? 'Buat akun' : 'Masuk' }}</h2>
                    <div class="auth-tabs" aria-label="Pilihan form">
                        <a href="{{ route('login') }}" class="auth-tab {{ $mode === 'login' ? 'active' : '' }}">Masuk</a>
                        <a href="{{ route('register') }}" class="auth-tab {{ $mode === 'register' ? 'active' : '' }}">Daftar</a>
                    </div>
                </div>

                @if ($mode === 'login')
                    @php
                        $loginUsesEmail = old('role', 'siswa') !== 'siswa';
                    @endphp
                    <form class="auth-form" method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="auth-form-header">
                            <h3>Selamat datang kembali</h3>
                            <p>Masuk ke dashboard sesuai peran Anda.</p>
                        </div>

                        <div class="field-grid">
                            <div class="field full">
                                <label for="login-role">Peran</label>
                                <select id="login-role" name="role" required>
                                    <option value="siswa" {{ old('role', 'siswa') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                    <option value="petugas_pmr" {{ old('role') === 'petugas_pmr' ? 'selected' : '' }}>Petugas PMR</option>
                                    <option value="wali_kelas" {{ old('role') === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')<span class="field-error-text">{{ $message }}</span>@enderror
                            </div>

                            <div class="field full">
                                <label id="login-identifier-label" for="login-identifier">{{ $loginUsesEmail ? 'Email' : 'NISN' }}</label>
                                <input id="login-identifier" name="identifier" type="{{ $loginUsesEmail ? 'email' : 'text' }}" inputmode="{{ $loginUsesEmail ? 'email' : 'numeric' }}" autocomplete="{{ $loginUsesEmail ? 'username' : 'off' }}" value="{{ old('identifier') }}" placeholder="{{ $loginUsesEmail ? 'nama@contoh.com' : 'Masukkan NISN siswa' }}" aria-describedby="login-identifier-hint @error('identifier')login-identifier-error @enderror" required>
                                <small class="field-hint" id="login-identifier-hint">{{ $loginUsesEmail ? 'Gunakan alamat email yang terdaftar.' : 'Gunakan NISN yang terdaftar.' }}</small>
                                @error('identifier')<span class="field-error-text" id="login-identifier-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field full">
                                <label for="login-password">Password</label>
                                <span class="password-control">
                                    <input id="login-password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password" required>
                                    <button class="password-toggle" type="button" data-password-toggle="login-password" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path class="eye-open" d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle class="eye-open" cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/><path class="eye-slash" d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.1 0 9.5 6 9.5 6a14.2 14.2 0 0 1-3.1 3.6M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.4 6 9.5 6c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                </span>
                                @error('password')<span class="field-error-text">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="field-error" role="alert" aria-live="polite">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="switch-row">
                            <label>
                                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                                Ingat saya
                            </label>
                        </div>

                        <button type="submit" class="auth-submit">Masuk ke dashboard</button>

                        <div class="auth-subtle">
                            <span>Belum punya akun?</span>
                            <a href="{{ route('register') }}">Daftar sekarang</a>
                        </div>
                    </form>
                @else
                    @php
                        $selectedRole = $selectedRole ?? 'siswa';
                        $adminManagedRole = in_array($selectedRole, ['petugas_pmr', 'wali_kelas', 'admin'], true);
                    @endphp

                    <form class="auth-form" method="POST" action="{{ route('register.store', ['role' => $selectedRole]) }}">
                        @csrf

                        @if (! $adminManagedRole)
                        <div class="auth-form-header">
                            <h3>Lengkapi data akun</h3>
                            <p>Pilih peran Anda lalu isi form yang sesuai.</p>
                        </div>
                        @endif

                        <div class="role-grid" aria-label="Pilih peran">
                            <button type="button" class="role-option {{ $selectedRole === 'siswa' ? 'is-selected' : '' }}" data-role="siswa" aria-pressed="{{ $selectedRole === 'siswa' ? 'true' : 'false' }}">
                                <div>
                                    <strong>Siswa</strong>
                                    <small>Daftar akun siswa</small>
                                </div>
                                <span class="role-badge">S</span>
                            </button>

                            <button type="button" class="role-option {{ $selectedRole === 'petugas_pmr' ? 'is-selected' : '' }}" data-role="petugas_pmr" aria-pressed="{{ $selectedRole === 'petugas_pmr' ? 'true' : 'false' }}">
                                <div>
                                    <strong>Petugas PMR</strong>
                                    <small>Dibuat oleh admin</small>
                                </div>
                                <span class="role-badge">P</span>
                            </button>

                            <button type="button" class="role-option {{ $selectedRole === 'wali_kelas' ? 'is-selected' : '' }}" data-role="wali_kelas" aria-pressed="{{ $selectedRole === 'wali_kelas' ? 'true' : 'false' }}">
                                <div>
                                    <strong>Wali Kelas</strong>
                                    <small>Dibuat oleh admin</small>
                                </div>
                                <span class="role-badge">W</span>
                            </button>

                            <button type="button" class="role-option {{ $selectedRole === 'admin' ? 'is-selected' : '' }}" data-role="admin" aria-pressed="{{ $selectedRole === 'admin' ? 'true' : 'false' }}">
                                <div>
                                    <strong>Admin</strong>
                                    <small>Hanya dibuat oleh admin</small>
                                </div>
                                <span class="role-badge">A</span>
                            </button>
                        </div>

                        @if (! $adminManagedRole)
                        <div class="field-grid">
                            <div class="field full">
                                <label for="name">Nama lengkap</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nama sesuai identitas" autocomplete="name" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" required>
                                @error('name')<span class="field-error-text">{{ $message }}</span>@enderror
                            </div>

                            <div class="field full">
                                <label for="email">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@contoh.com" autocomplete="email" inputmode="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" required>
                                @error('email')<span class="field-error-text">{{ $message }}</span>@enderror
                            </div>

                            <div class="field">
                                <label for="password">Password</label>
                                <span class="password-control"><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" placeholder="Minimal 8 karakter" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" required><button class="password-toggle" type="button" data-password-toggle="password" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path class="eye-open" d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle class="eye-open" cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/><path class="eye-slash" d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.1 0 9.5 6 9.5 6a14.2 14.2 0 0 1-3.1 3.6M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.4 6 9.5 6c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span>
                                @error('password')<span class="field-error-text">{{ $message }}</span>@enderror
                            </div>

                            <div class="field">
                                <label for="password_confirmation">Konfirmasi password</label>
                                <span class="password-control"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" placeholder="Ulangi password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" required><button class="password-toggle" type="button" data-password-toggle="password_confirmation" aria-label="Tampilkan konfirmasi password" aria-pressed="false" title="Tampilkan konfirmasi password"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path class="eye-open" d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle class="eye-open" cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/><path class="eye-slash" d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.1 0 9.5 6 9.5 6a14.2 14.2 0 0 1-3.1 3.6M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.4 6 9.5 6c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span>
                                @error('password')<span class="field-error-text">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="role-section {{ $selectedRole === 'siswa' ? 'active' : '' }}" data-role-section="siswa">
                            <div class="field-grid">
                                <div class="field full">
                                    <label for="nisn">NISN</label>
                                    <input id="nisn" name="nisn" type="text" value="{{ old('nisn') }}" placeholder="Masukkan NISN" inputmode="numeric" autocomplete="off" aria-invalid="{{ $errors->has('nisn') ? 'true' : 'false' }}" required @disabled($selectedRole !== 'siswa')>
                                    @error('nisn')<span class="field-error-text">{{ $message }}</span>@enderror
                                </div>

                                <div class="field">
                                    <label for="kelas">Kelas</label>
                                    <select id="kelas" name="kelas" aria-invalid="{{ $errors->has('kelas') ? 'true' : 'false' }}" required @disabled($selectedRole !== 'siswa')>
                                        <option value="">Pilih kelas</option>
                                        <optgroup label="BRP">
                                            @for ($number = 1; $number <= 6; $number++)
                                                <option value="BRP {{ $number }}" {{ old('kelas') === "BRP {$number}" ? 'selected' : '' }}>BRP {{ $number }}</option>
                                            @endfor
                                        </optgroup>
                                        <optgroup label="RPL">
                                            @for ($number = 1; $number <= 4; $number++)
                                                <option value="RPL {{ $number }}" {{ old('kelas') === "RPL {$number}" ? 'selected' : '' }}>RPL {{ $number }}</option>
                                            @endfor
                                        </optgroup>
                                        <optgroup label="DKV">
                                            @for ($number = 1; $number <= 3; $number++)
                                                <option value="DKV {{ $number }}" {{ old('kelas') === "DKV {$number}" ? 'selected' : '' }}>DKV {{ $number }}</option>
                                            @endfor
                                        </optgroup>
                                    </select>
                                    @error('kelas')<span class="field-error-text">{{ $message }}</span>@enderror
                                </div>

                                <div class="field">
                                    <label for="jenis_kelamin">Jenis kelamin</label>
                                    <select id="jenis_kelamin" name="jenis_kelamin" required @disabled($selectedRole !== 'siswa')>
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')<span class="field-error-text">{{ $message }}</span>@enderror
                                </div>
                                <div class="field full">
                                    <label for="nomor_whatsapp_orang_tua">Nomor WhatsApp orang tua/wali</label>
                                    <input id="nomor_whatsapp_orang_tua" name="nomor_whatsapp_orang_tua" type="tel" value="{{ old('nomor_whatsapp_orang_tua') }}" placeholder="Contoh: 081234567890" autocomplete="tel" aria-invalid="{{ $errors->has('nomor_whatsapp_orang_tua') ? 'true' : 'false' }}" required @disabled($selectedRole !== 'siswa')>
                                    @error('nomor_whatsapp_orang_tua')<span class="field-error-text">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="role-section {{ $selectedRole === 'petugas_pmr' ? 'active' : '' }}" data-role-section="petugas_pmr"></div>

                        <div class="role-section {{ $selectedRole === 'wali_kelas' ? 'active' : '' }}" data-role-section="wali_kelas">
                            <div class="field-grid">
                                <div class="field full">
                                    <label for="kelas_dampu">Kelas dampu</label>
                                    <select id="kelas_dampu" name="kelas_dampu" aria-invalid="{{ $errors->has('kelas_dampu') ? 'true' : 'false' }}" required @disabled($selectedRole !== 'wali_kelas')>
                                        <option value="">Pilih kelas dampu</option>
                                        <optgroup label="BRP">
                                            @for ($number = 1; $number <= 6; $number++)
                                                <option value="BRP {{ $number }}" {{ old('kelas_dampu') === "BRP {$number}" ? 'selected' : '' }}>BRP {{ $number }}</option>
                                            @endfor
                                        </optgroup>
                                        <optgroup label="RPL">
                                            @for ($number = 1; $number <= 4; $number++)
                                                <option value="RPL {{ $number }}" {{ old('kelas_dampu') === "RPL {$number}" ? 'selected' : '' }}>RPL {{ $number }}</option>
                                            @endfor
                                        </optgroup>
                                        <optgroup label="DKV">
                                            @for ($number = 1; $number <= 3; $number++)
                                                <option value="DKV {{ $number }}" {{ old('kelas_dampu') === "DKV {$number}" ? 'selected' : '' }}>DKV {{ $number }}</option>
                                            @endfor
                                        </optgroup>
                                    </select>
                                    @error('kelas_dampu')<span class="field-error-text">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="field-error" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button type="submit" class="auth-submit">Daftar akun</button>

                        <div class="auth-subtle">
                            <span>Sudah punya akun?</span>
                            <a href="{{ route('login') }}">Masuk di sini</a>
                        </div>
                        @else
                        <div class="helper-box admin" role="status">
                            @if ($selectedRole === 'petugas_pmr')
                                Akun Petugas PMR hanya dapat dibuat oleh admin setelah identitas dan penugasan diverifikasi.
                            @elseif ($selectedRole === 'wali_kelas')
                                Akun Wali Kelas hanya dapat dibuat oleh admin setelah identitas dan kelas dampu diverifikasi.
                            @else
                                Pendaftaran akun Admin hanya bisa dilakukan dengan meminta administrator sekolah membuatkan akun untuk Anda.
                            @endif
                            @if (auth()->check() && auth()->user()->role === 'admin' && $selectedRole === 'petugas_pmr')
                                <a href="{{ route('admin.pmr.index') }}">Buka pengelolaan Petugas PMR</a>
                            @elseif (auth()->check() && auth()->user()->role === 'admin' && $selectedRole === 'wali_kelas')
                                <a href="{{ route('admin.wali-kelas.index') }}">Buka pengelolaan Wali Kelas</a>
                            @endif
                        </div>
                        @endif
                    </form>
                @endif
            </section>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
            const input = document.getElementById(toggle.dataset.passwordToggle);

            if (!input) {
                return;
            }

            toggle.addEventListener('click', () => {
                const showPassword = input.type === 'password';
                input.type = showPassword ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', String(showPassword));
                toggle.setAttribute('aria-label', showPassword ? 'Sembunyikan password' : 'Tampilkan password');
                toggle.title = showPassword ? 'Sembunyikan password' : 'Tampilkan password';
            });
        });

        const loginRole = document.querySelector('#login-role');
        const loginIdentifier = document.querySelector('#login-identifier');
        const loginIdentifierLabel = document.querySelector('#login-identifier-label');
        const loginIdentifierHint = document.querySelector('#login-identifier-hint');

        if (loginRole && loginIdentifier && loginIdentifierLabel && loginIdentifierHint) {
            let previousUsesEmail = loginRole.value !== 'siswa';
            const updateLoginIdentifier = (clearOnTypeChange = false) => {
                const usesEmail = loginRole.value !== 'siswa';

                if (clearOnTypeChange && usesEmail !== previousUsesEmail) {
                    loginIdentifier.value = '';
                }

                loginIdentifierLabel.textContent = usesEmail ? 'Email' : 'NISN';
                loginIdentifier.type = usesEmail ? 'email' : 'text';
                loginIdentifier.inputMode = usesEmail ? 'email' : 'numeric';
                loginIdentifier.autocomplete = usesEmail ? 'username' : 'off';
                loginIdentifier.placeholder = usesEmail ? 'nama@contoh.com' : 'Masukkan NISN siswa';
                loginIdentifierHint.textContent = usesEmail
                    ? 'Gunakan alamat email yang terdaftar.'
                    : 'Gunakan NISN yang terdaftar.';
                previousUsesEmail = usesEmail;
            };

            updateLoginIdentifier();
            loginRole.addEventListener('change', () => updateLoginIdentifier(true));
        }

        const roleButtons = document.querySelectorAll('.role-option');
        const roleSections = document.querySelectorAll('.role-section');
        const registerForm = document.querySelector('form[action*="register"]');

        roleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const role = button.dataset.role;
                const targetUrl = new URL(window.location.href);
                targetUrl.pathname = '/register/' + role;
                window.location.href = targetUrl.toString();
            });
        });

        if (registerForm && roleButtons.length) {
            const activeRole = document.querySelector('.role-option.is-selected')?.dataset.role || 'siswa';
            roleSections.forEach((section) => {
                const isActive = section.dataset.roleSection === activeRole;
                section.classList.toggle('active', isActive);
                section.querySelectorAll('input, select, textarea').forEach((field) => {
                    field.disabled = !isActive;
                });
            });

            const submitButton = registerForm.querySelector('.auth-submit');
            if (activeRole === 'admin' && submitButton) {
                submitButton.disabled = true;
                submitButton.style.opacity = '0.7';
                submitButton.style.cursor = 'not-allowed';
                submitButton.textContent = 'Admin tidak dapat didaftarkan sendiri';
            }
        }
    </script>
</body>
</html>
