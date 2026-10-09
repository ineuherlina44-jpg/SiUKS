<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eaf3fc">
    <title>Data Wali Kelas | SiUKS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body">
<div class="siuks-app siuks-student-page siuks-wali-admin-page" data-validation-errors="{{ $errors->any() ? 'true' : 'false' }}">
    <aside class="siuks-sidebar">
        <a class="siuks-brand" href="{{ route('dashboard') }}" aria-label="SiUKS, dashboard"><span class="siuks-brand-icon" aria-hidden="true"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""></span><span class="siuks-brand-text"><strong>SiUKS</strong><small>Sistem Informasi Kesehatan Sekolah</small></span></a>
        <nav class="siuks-navigation" aria-label="Navigasi utama">
            <a class="siuks-nav-item" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z"/></svg><span>Dashboard</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.siswa.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20v-1.3A5.7 5.7 0 0 1 8.2 13h1.6a5.7 5.7 0 0 1 5.7 5.7V20H2.5ZM17 5a3.2 3.2 0 0 1 0 6.2M17 14a4.7 4.7 0 0 1 4.5 4.7V20h-4"/></svg><span>Data Siswa</span></a>
            <a class="siuks-nav-item is-active" href="{{ route('admin.wali-kelas.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20v-1a6.5 6.5 0 0 1 13 0v1h-13ZM15 14a5 5 0 0 1 6.5 4.8V20H17"/></svg><span>Data Wali Kelas</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.pmr.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7.5" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg><span>Data Petugas PMR</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.obat.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.1 3.5a5.5 5.5 0 0 1 7.8 0l4.6 4.6a5.5 5.5 0 0 1-7.8 7.8l-4.6-4.6a5.5 5.5 0 0 1 0-7.8Z"/><path d="m9.2 12 5.6-5.6" stroke="#fff" stroke-width="1.6"/></svg><span>Master Obat</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.rekam-medis.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Rekam Medis</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.sos.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 22 20H2L12 3Z"/><path d="M12 9v5m0 3h.01" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg><span>Monitoring SOS</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.laporan.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Laporan</span></a>
        </nav>
        <div class="siuks-sidebar-foot"><span class="siuks-sidebar-status"></span>Sistem UKS Sekolah<small>v1.0</small></div>
    </aside>
    <main class="siuks-main">
        <header class="siuks-topbar">
            <button class="siuks-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi">⋯</button>
            <div class="siuks-top-context"><span>Panel Admin</span><strong>Data Wali Kelas</strong></div>
            <div class="siuks-top-actions"><div class="siuks-admin"><span class="siuks-admin-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="siuks-logout" type="submit">Logout</button></form></div>
        </header>
        <div class="siuks-content">
            <section class="siuks-heading"><div><h1>Data Wali Kelas</h1><p>Kelola akun, jurusan, dan kelas dampu wali kelas</p></div><button class="siuks-student-add" type="button" data-open-admin-wali-create>＋ Tambah wali kelas</button></section>
            @if (session('status'))<div class="siuks-student-notice notice-success" role="status">{{ session('status') }}</div>@endif
            @if (session('error'))<div class="siuks-student-notice notice-error" role="alert">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="siuks-student-notice notice-error" role="alert">Periksa kembali data yang dimasukkan pada formulir.</div>@endif
            <section class="siuks-student-stats" aria-label="Ringkasan data wali kelas">
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-blue" aria-hidden="true">♙</span><div><span>Total wali kelas</span><strong>{{ $totalTeachers }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate" aria-hidden="true">✓</span><div><span>Akun aktif</span><strong>{{ $activeTeachers }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-red" aria-hidden="true">▦</span><div><span>Kelas dampu</span><strong>{{ $assignedClasses }}</strong></div></article>
            </section>
            <section class="siuks-panel siuks-student-panel">
                <div class="siuks-student-panel-heading"><div><h2>Daftar wali kelas</h2><p>Kelola identitas, penugasan kelas, dan status akun.</p></div></div>
                <form class="siuks-student-filters siuks-wali-filters" method="GET" action="{{ route('admin.wali-kelas.index') }}">
                    <label><span>Cari nama atau NIP</span><input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Masukkan nama atau NIP"></label>
                    <label><span>Jurusan</span><select name="jurusan"><option value="">Semua jurusan</option>@foreach ($jurusans as $major)<option value="{{ $major }}" @selected(($filters['jurusan'] ?? '') === $major)>{{ $major }}</option>@endforeach</select></label>
                    <label><span>Kelas</span><select name="kelas"><option value="">Semua kelas</option>@foreach ($classes as $class)<option value="{{ $class }}" @selected(($filters['kelas'] ?? '') === $class)>{{ $class }}</option>@endforeach</select></label>
                    <button type="submit" class="siuks-student-filter-submit">Cari wali</button>
                </form>
                <div class="siuks-table-scroll siuks-student-table-wrap">
                    <table class="siuks-student-table siuks-wali-table"><thead><tr><th>Nama wali kelas</th><th>NIP</th><th>Kelas</th><th>Jurusan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td><strong>{{ $teacher->name }}</strong><small>{{ $teacher->email }}</small></td>
                                <td>{{ $teacher->nip ?: '—' }}</td>
                                <td>{{ $teacher->kelas_dampu }}</td>
                                <td>{{ $teacher->jurusan ?: '—' }}</td>
                                <td><span class="siuks-pill {{ $teacher->status === 'Aktif' ? 'pill-healthy' : 'pill-inactive' }}">{{ $teacher->status }}</span></td>
                                <td><div class="siuks-student-actions">
                                    <button type="button" class="siuks-student-edit" data-edit-admin-wali data-id="{{ $teacher->id }}" data-url="{{ route('admin.wali-kelas.update', $teacher->id) }}" data-name="{{ $teacher->name }}" data-email="{{ $teacher->email }}" data-nip="{{ $teacher->nip }}" data-class="{{ $teacher->kelas_dampu }}" data-major="{{ $teacher->jurusan }}" data-status="{{ $teacher->status }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.wali-kelas.destroy', $teacher->id) }}" data-delete-admin-wali>@csrf @method('DELETE')<button type="submit" class="siuks-student-delete">Hapus</button></form>
                                </div></td>
                            </tr>
                        @empty
                            <tr><td class="siuks-student-empty" colspan="6">Belum ada data wali kelas yang cocok dengan pencarian ini.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
                @if ($teachers->hasPages())<div class="siuks-student-pagination">{{ $teachers->links() }}</div>@endif
            </section>
            <footer class="siuks-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data wali kelas seluruh sekolah</span></footer>
        </div>
    </main>
    <dialog class="siuks-student-dialog" id="admin-wali-create-dialog" data-admin-wali-dialog data-kind="create">
        <form method="POST" action="{{ route('admin.wali-kelas.store') }}" class="siuks-student-form" data-admin-wali-form>
            @csrf
            <div class="siuks-student-dialog-heading"><div><p>Akun wali kelas baru</p><h2>Tambah wali kelas</h2></div><button type="button" data-close-admin-wali-dialog aria-label="Tutup">×</button></div>
            @foreach (['name' => 'Nama lengkap', 'email' => 'Email akun', 'nip' => 'NIP', 'jurusan' => 'Jurusan', 'kelas_dampu' => 'Kelas dampu'] as $field => $label)
                <label><span>{{ $label }}</span><input name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old($field) }}" required maxlength="{{ $field === 'nip' ? 30 : (in_array($field, ['jurusan', 'kelas_dampu'], true) ? 100 : 255) }}">@error($field)<small>{{ $message }}</small>@enderror</label>
            @endforeach
            <label><span>Password awal</span><input name="password" type="password" minlength="8" required autocomplete="new-password">@error('password')<small>{{ $message }}</small>@enderror</label>
            <label><span>Konfirmasi password</span><input name="password_confirmation" type="password" minlength="8" required autocomplete="new-password"></label>
            <label><span>Status</span><select name="status" required><option value="Aktif" @selected(old('status', 'Aktif') === 'Aktif')>Aktif</option><option value="Nonaktif" @selected(old('status') === 'Nonaktif')>Nonaktif</option></select>@error('status')<small>{{ $message }}</small>@enderror</label>
            <p class="siuks-student-form-note">Akun wali kelas menggunakan email dan password untuk masuk. Akun nonaktif tidak dapat login.</p>
            <div class="siuks-student-form-actions"><button type="button" data-close-admin-wali-dialog>Batal</button><button type="submit">Simpan wali kelas</button></div>
        </form>
    </dialog>
    <dialog class="siuks-student-dialog" id="admin-wali-edit-dialog" data-admin-wali-dialog data-kind="edit">
        <form method="POST" class="siuks-student-form" data-admin-wali-form data-old-name="{{ old('name') }}" data-old-email="{{ old('email') }}" data-old-nip="{{ old('nip') }}" data-old-class="{{ old('kelas_dampu') }}" data-old-major="{{ old('jurusan') }}" data-old-status="{{ old('status') }}">
            @csrf
            @method('PUT')
            <div class="siuks-student-dialog-heading"><div><p>Kelola data wali kelas</p><h2>Edit wali kelas</h2></div><button type="button" data-close-admin-wali-dialog aria-label="Tutup">×</button></div>
            @foreach (['name' => 'Nama lengkap', 'email' => 'Email akun', 'nip' => 'NIP', 'jurusan' => 'Jurusan', 'kelas_dampu' => 'Kelas dampu'] as $field => $label)
                <label><span>{{ $label }}</span><input name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" required maxlength="{{ $field === 'nip' ? 30 : (in_array($field, ['jurusan', 'kelas_dampu'], true) ? 100 : 255) }}">@error($field)<small>{{ $message }}</small>@enderror</label>
            @endforeach
            <label><span>Status</span><select name="status" required><option value="Aktif">Aktif</option><option value="Nonaktif">Nonaktif</option></select>@error('status')<small>{{ $message }}</small>@enderror</label>
            <div class="siuks-student-form-actions"><button type="button" data-close-admin-wali-dialog>Batal</button><button type="submit">Simpan perubahan</button></div>
        </form>
    </dialog>
</div>
</body>
</html>
