<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eaf3fc">
    <title>Data Petugas PMR | SiUKS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body">
<div class="siuks-app siuks-student-page siuks-pmr-admin-page" data-validation-errors="{{ $errors->any() ? 'true' : 'false' }}">
    <aside class="siuks-sidebar">
        <a class="siuks-brand" href="{{ route('dashboard') }}" aria-label="SiUKS, dashboard"><span class="siuks-brand-icon" aria-hidden="true"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""></span><span class="siuks-brand-text"><strong>SiUKS</strong><small>Sistem Informasi Kesehatan Sekolah</small></span></a>
        <nav class="siuks-navigation" aria-label="Navigasi utama">
            <a class="siuks-nav-item" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z"/></svg><span>Dashboard</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.siswa.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20v-1.3A5.7 5.7 0 0 1 8.2 13h1.6a5.7 5.7 0 0 1 5.7 5.7V20H2.5ZM17 5a3.2 3.2 0 0 1 0 6.2M17 14a4.7 4.7 0 0 1 4.5 4.7V20h-4"/></svg><span>Data Siswa</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.wali-kelas.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20v-1a6.5 6.5 0 0 1 13 0v1h-13ZM15 14a5 5 0 0 1 6.5 4.8V20H17"/></svg><span>Data Wali Kelas</span></a>
            <a class="siuks-nav-item is-active" href="{{ route('admin.pmr.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7.5" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg><span>Data Petugas PMR</span></a>
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
            <div class="siuks-top-context"><span>Panel Admin</span><strong>Data Petugas PMR</strong></div>
            <div class="siuks-top-actions"><div class="siuks-admin"><span class="siuks-admin-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="siuks-logout" type="submit">Logout</button></form></div>
        </header>
        <div class="siuks-content">
            <section class="siuks-heading"><div><h1>Data Petugas PMR</h1><p>Kelola akun dan penugasan petugas PMR</p></div><button class="siuks-student-add" type="button" data-open-admin-pmr-create>＋ Tambah petugas</button></section>
            @if (session('status'))<div class="siuks-student-notice notice-success" role="status">{{ session('status') }}</div>@endif
            @if (session('error'))<div class="siuks-student-notice notice-error" role="alert">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="siuks-student-notice notice-error" role="alert">Periksa kembali data yang dimasukkan pada formulir.</div>@endif
            <section class="siuks-student-stats" aria-label="Ringkasan data petugas PMR">
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-blue" aria-hidden="true">♙</span><div><span>Total petugas</span><strong>{{ $totalStaff }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate" aria-hidden="true">✓</span><div><span>Petugas aktif</span><strong>{{ $activeStaff }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-red" aria-hidden="true">▦</span><div><span>Kelas terlayani</span><strong>{{ $assignedClasses }}</strong></div></article>
            </section>
            <section class="siuks-panel siuks-student-panel">
                <div class="siuks-student-panel-heading"><div><h2>Daftar petugas PMR</h2><p>Cari petugas menurut nama, NIS, kelas, atau status keaktifan.</p></div></div>
                <form class="siuks-student-filters siuks-pmr-filters" method="GET" action="{{ route('admin.pmr.index') }}">
                    <label><span>Cari nama, NIS, atau kelas</span><input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Masukkan kata pencarian"></label>
                    <label><span>Kelas</span><select name="kelas"><option value="">Semua kelas</option>@foreach ($classes as $class)<option value="{{ $class }}" @selected(($filters['kelas'] ?? '') === $class)>{{ $class }}</option>@endforeach</select></label>
                    <label><span>Status</span><select name="status"><option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Semua status</option><option value="Aktif" @selected(($filters['status'] ?? '') === 'Aktif')>Aktif</option><option value="Nonaktif" @selected(($filters['status'] ?? '') === 'Nonaktif')>Nonaktif</option></select></label>
                    <button type="submit" class="siuks-student-filter-submit">Cari petugas</button>
                </form>
                <div class="siuks-table-scroll siuks-student-table-wrap">
                    <table class="siuks-student-table siuks-pmr-table"><thead><tr><th>Nama petugas</th><th>NIS</th><th>Kelas</th><th>Jenis kelamin</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                        @forelse ($staff as $member)
                            <tr>
                                <td><strong>{{ $member->name }}</strong><small>{{ $member->email }}</small></td>
                                <td>{{ $member->nis ?: '—' }}</td>
                                <td>{{ $member->kelas ?: '—' }}</td>
                                <td>{{ $member->jenis_kelamin ?: '—' }}</td>
                                <td><span class="siuks-pill {{ $member->status === 'Aktif' ? 'pill-healthy' : 'pill-inactive' }}">{{ $member->status ?: 'Aktif' }}</span></td>
                                <td><div class="siuks-student-actions">
                                    <button type="button" class="siuks-student-edit" data-edit-admin-pmr data-id="{{ $member->id }}" data-url="{{ route('admin.pmr.update', $member->id) }}" data-name="{{ $member->name }}" data-email="{{ $member->email }}" data-nis="{{ $member->nis }}" data-class="{{ $member->kelas }}" data-gender="{{ $member->jenis_kelamin }}" data-status="{{ $member->status ?: 'Aktif' }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.pmr.destroy', $member->id) }}" data-delete-admin-pmr>@csrf @method('DELETE')<button type="submit" class="siuks-student-delete">Hapus</button></form>
                                </div></td>
                            </tr>
                        @empty
                            <tr><td class="siuks-student-empty" colspan="6">Belum ada data petugas PMR yang cocok dengan pencarian ini.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
                @if ($staff->hasPages())<div class="siuks-student-pagination">{{ $staff->links() }}</div>@endif
            </section>
            <footer class="siuks-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data petugas PMR sekolah</span></footer>
        </div>
    </main>
    <dialog class="siuks-student-dialog" id="admin-pmr-create-dialog" data-admin-pmr-dialog data-kind="create">
        <form method="POST" action="{{ route('admin.pmr.store') }}" class="siuks-student-form" data-admin-pmr-form>
            @csrf
            <div class="siuks-student-dialog-heading"><div><p>Akun petugas PMR baru</p><h2>Tambah petugas</h2></div><button type="button" data-close-admin-pmr-dialog aria-label="Tutup">×</button></div>
            @foreach (['name' => 'Nama lengkap', 'email' => 'Email akun', 'nis' => 'NIS', 'kelas' => 'Kelas'] as $field => $label)
                <label><span>{{ $label }}</span><input name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old($field) }}" required maxlength="{{ in_array($field, ['nis', 'kelas'], true) ? 100 : 255 }}">@error($field)<small>{{ $message }}</small>@enderror</label>
            @endforeach
            <label><span>Jenis kelamin</span><select name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><option value="Laki-laki" @selected(old('jenis_kelamin') === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected(old('jenis_kelamin') === 'Perempuan')>Perempuan</option></select>@error('jenis_kelamin')<small>{{ $message }}</small>@enderror</label>
            <label><span>Password awal</span><input name="password" type="password" minlength="8" required autocomplete="new-password">@error('password')<small>{{ $message }}</small>@enderror</label>
            <label><span>Konfirmasi password</span><input name="password_confirmation" type="password" minlength="8" required autocomplete="new-password"></label>
            <label><span>Status akun</span><select name="status" required><option value="Aktif" @selected(old('status', 'Aktif') === 'Aktif')>Aktif</option><option value="Nonaktif" @selected(old('status') === 'Nonaktif')>Nonaktif</option></select>@error('status')<small>{{ $message }}</small>@enderror</label>
            <p class="siuks-student-form-note">Petugas menggunakan email dan password untuk masuk. Akun nonaktif tidak dapat login.</p>
            <div class="siuks-student-form-actions"><button type="button" data-close-admin-pmr-dialog>Batal</button><button type="submit">Simpan petugas</button></div>
        </form>
    </dialog>
    <dialog class="siuks-student-dialog" id="admin-pmr-edit-dialog" data-admin-pmr-dialog data-kind="edit">
        <form method="POST" class="siuks-student-form" data-admin-pmr-form data-old-name="{{ old('name') }}" data-old-email="{{ old('email') }}" data-old-nis="{{ old('nis') }}" data-old-class="{{ old('kelas') }}" data-old-gender="{{ old('jenis_kelamin') }}" data-old-status="{{ old('status') }}">
            @csrf
            @method('PUT')
            <div class="siuks-student-dialog-heading"><div><p>Kelola data petugas</p><h2>Edit petugas PMR</h2></div><button type="button" data-close-admin-pmr-dialog aria-label="Tutup">×</button></div>
            @foreach (['name' => 'Nama lengkap', 'email' => 'Email akun', 'nis' => 'NIS', 'kelas' => 'Kelas'] as $field => $label)
                <label><span>{{ $label }}</span><input name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" required maxlength="{{ in_array($field, ['nis', 'kelas'], true) ? 100 : 255 }}">@error($field)<small>{{ $message }}</small>@enderror</label>
            @endforeach
            <label><span>Jenis kelamin</span><select name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select>@error('jenis_kelamin')<small>{{ $message }}</small>@enderror</label>
            <label><span>Status akun</span><select name="status" required><option value="Aktif">Aktif</option><option value="Nonaktif">Nonaktif</option></select>@error('status')<small>{{ $message }}</small>@enderror</label>
            <div class="siuks-student-form-actions"><button type="button" data-close-admin-pmr-dialog>Batal</button><button type="submit">Simpan perubahan</button></div>
        </form>
    </dialog>
</div>
</body>
</html>
