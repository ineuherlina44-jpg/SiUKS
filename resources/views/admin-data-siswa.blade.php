<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eaf3fc">
    <title>Data Siswa | SiUKS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body">
<div class="siuks-app siuks-student-page" data-validation-errors="{{ $errors->any() ? 'true' : 'false' }}">
    <aside class="siuks-sidebar">
        <a class="siuks-brand" href="{{ route('dashboard') }}" aria-label="SiUKS, dashboard"><span class="siuks-brand-icon" aria-hidden="true"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""></span><span class="siuks-brand-text"><strong>SiUKS</strong><small>Sistem Informasi Kesehatan Sekolah</small></span></a>
        <nav class="siuks-navigation" aria-label="Navigasi utama">
            <a class="siuks-nav-item" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z"/></svg><span>Dashboard</span></a>
            <a class="siuks-nav-item is-active" href="{{ route('admin.siswa.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20v-1.3A5.7 5.7 0 0 1 8.2 13h1.6a5.7 5.7 0 0 1 5.7 5.7V20H2.5ZM17 5a3.2 3.2 0 0 1 0 6.2M17 14a4.7 4.7 0 0 1 4.5 4.7V20h-4"/></svg><span>Data Siswa</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.wali-kelas.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20v-1a6.5 6.5 0 0 1 13 0v1h-13ZM15 14a5 5 0 0 1 6.5 4.8V20H17"/></svg><span>Data Wali Kelas</span></a>
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
            <div class="siuks-top-context"><span>Panel Admin</span><strong>Data Siswa</strong></div>
            <div class="siuks-top-actions"><div class="siuks-admin"><span class="siuks-admin-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="siuks-logout" type="submit">Logout</button></form></div>
        </header>

        <div class="siuks-content">
            <section class="siuks-heading">
                <div><h1>Data Siswa</h1><p>Kelola data siswa dan akun seluruh kelas</p></div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <button class="siuks-student-add" style="background: #2E9B67; color: #fff; box-shadow: 0 4px 12px rgba(46, 155, 103, 0.25);" type="button" id="btn-open-admin-student-import">📥 Import Siswa per Kelas</button>
                    <button class="siuks-student-add" type="button" data-open-admin-student-create>＋ Tambah manual</button>
                </div>
            </section>

            @if (session('status'))
                <div class="siuks-student-notice notice-success" role="status">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="siuks-student-notice notice-error" role="alert">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="siuks-student-notice notice-error" role="alert">Periksa kembali data yang dimasukkan pada formulir.</div>
            @endif
            @include('partials.student-import-credentials')

            <section class="siuks-student-stats" aria-label="Ringkasan data siswa">
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-blue" aria-hidden="true">♙</span><div><span>Total siswa</span><strong>{{ $totalStudents }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-red" aria-hidden="true">!</span><div><span>Siswa sakit bulan ini</span><strong>{{ $sickStudents }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate" aria-hidden="true">▦</span><div><span>Total kelas</span><strong>{{ $totalClasses }}</strong></div></article>
            </section>

            <section class="siuks-panel siuks-student-panel">
                <div class="siuks-student-panel-heading"><div><h2>Daftar siswa</h2><p>Data kesehatan dihitung berdasarkan kunjungan UKS bulan ini.</p></div></div>
                <form class="siuks-student-filters" method="GET" action="{{ route('admin.siswa.index') }}">
                    <label><span>Cari nama atau NISN</span><input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Masukkan nama atau NISN"></label>
                    <label><span>Kelas</span><select name="kelas"><option value="">Semua kelas</option>@foreach ($classes as $class)<option value="{{ $class }}" @selected(($filters['kelas'] ?? '') === $class)>{{ $class }}</option>@endforeach</select></label>
                    <label><span>Status kesehatan</span><select name="status"><option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Semua status</option><option value="sakit" @selected(($filters['status'] ?? '') === 'sakit')>Sakit</option><option value="sehat" @selected(($filters['status'] ?? '') === 'sehat')>Sehat</option></select></label>
                    <button type="submit" class="siuks-student-filter-submit">Cari siswa</button>
                </form>
                <div class="siuks-table-scroll siuks-student-table-wrap">
                    <table class="siuks-student-table"><thead><tr><th>Nama siswa</th><th>NISN</th><th>Kelas</th><th>Jenis kelamin</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td><strong>{{ $student->nama_siswa }}</strong><small>{{ $student->email }}</small></td>
                                <td>{{ $student->nisn }}</td>
                                <td>{{ $student->kelas }}</td>
                                <td>{{ $student->jenis_kelamin }}</td>
                                <td><span class="siuks-pill {{ $student->berkunjung_bulan_ini ? 'pill-sick' : 'pill-healthy' }}">{{ $student->berkunjung_bulan_ini ? 'Sakit' : 'Sehat' }}</span></td>
                                <td><div class="siuks-student-actions">
                                    <button type="button" class="siuks-student-edit" data-edit-admin-student data-id="{{ $student->id }}" data-url="{{ route('admin.siswa.update', $student->id) }}" data-name="{{ $student->nama_siswa }}" data-email="{{ $student->email }}" data-nisn="{{ $student->nisn }}" data-class="{{ $student->kelas }}" data-gender="{{ $student->jenis_kelamin }}" data-allergies="{{ $student->riwayat_alergi }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.siswa.destroy', $student->id) }}" data-delete-admin-student>@csrf @method('DELETE')<button type="submit" class="siuks-student-delete">Hapus</button></form>
                                </div></td>
                            </tr>
                        @empty
                            <tr><td class="siuks-student-empty" colspan="6">Belum ada data siswa yang cocok dengan pencarian ini.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
                @if ($students->hasPages())
                    <div class="siuks-student-pagination">{{ $students->links() }}</div>
                @endif
            </section>
            <footer class="siuks-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data siswa seluruh kelas</span></footer>
        </div>
    </main>

    <dialog class="siuks-student-dialog" id="admin-student-create-dialog" data-admin-student-dialog data-kind="create">
        <form method="POST" action="{{ route('admin.siswa.store') }}" class="siuks-student-form" data-admin-student-form>
            @csrf
            <div class="siuks-student-dialog-heading"><div><p>Akun siswa baru</p><h2>Tambah siswa</h2></div><button type="button" data-close-admin-student-dialog aria-label="Tutup">×</button></div>
            @foreach (['name' => 'Nama lengkap', 'email' => 'Email akun', 'nisn' => 'NISN', 'kelas' => 'Kelas'] as $field => $label)
                <label><span>{{ $label }}</span><input name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old($field) }}" required maxlength="{{ $field === 'nisn' ? 20 : 255 }}">@error($field)<small>{{ $message }}</small>@enderror</label>
            @endforeach
            <label><span>Password awal siswa</span><input name="password" type="password" minlength="8" required autocomplete="new-password">@error('password')<small>{{ $message }}</small>@enderror</label>
            <label><span>Konfirmasi password</span><input name="password_confirmation" type="password" minlength="8" required autocomplete="new-password"></label>
            <label><span>Jenis kelamin</span><select name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><option value="Laki-laki" @selected(old('jenis_kelamin') === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected(old('jenis_kelamin') === 'Perempuan')>Perempuan</option></select>@error('jenis_kelamin')<small>{{ $message }}</small>@enderror</label>
            <label><span>Riwayat alergi (opsional)</span><textarea name="riwayat_alergi" rows="3" maxlength="2000">{{ old('riwayat_alergi') }}</textarea>@error('riwayat_alergi')<small>{{ $message }}</small>@enderror</label>
            <p class="siuks-student-form-note">Siswa akan login menggunakan NISN dan password awal yang dibuat di sini.</p>
            <div class="siuks-student-form-actions"><button type="button" data-close-admin-student-dialog>Batal</button><button type="submit">Simpan siswa</button></div>
        </form>
    </dialog>
    <dialog class="siuks-student-dialog" id="admin-student-import-dialog" style="max-width: 520px;">
        <form method="POST" action="{{ route('admin.siswa.import') }}" enctype="multipart/form-data" class="siuks-student-form">
            @csrf
            <div class="siuks-student-dialog-heading">
                <div>
                    <p style="color: #2E9B67; font-weight: 700;">Import Otomatis Siswa</p>
                    <h2>Pilih Kelas &amp; Daftarkan Semua Siswa</h2>
                </div>
                <button type="button" class="btn-close-admin-import" aria-label="Tutup">×</button>
            </div>

            <div style="background: #EAF3FF; border: 1px solid rgba(23, 50, 77, 0.12); padding: 12px 14px; border-radius: 10px; margin-bottom: 14px;">
                <p style="font-size: 11px; color: #17324D; margin: 0; line-height: 1.5;">
                    💡 <strong>Otomatis Tanpa Input Manual:</strong> Pilih kelas untuk langsung mendaftarkan seluruh siswa kelas tersebut (lengkap dengan NISN, nama, jenis kelamin, dan akun login siswa).
                </p>
            </div>

            <div style="display: flex; gap: 8px; margin-bottom: 14px;">
                <label id="lbl_admin_type_class" style="flex: 1; display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #2E9B67; background: #fff; border-radius: 8px; cursor: pointer; font-size: 11px; font-weight: 700; color: #17324D;">
                    <input type="radio" name="import_type" value="class" id="admin_import_type_class" @checked(in_array(config('app.env'), ['local', 'testing'], true)) @disabled(! in_array(config('app.env'), ['local', 'testing'], true))>
                    <span>{{ in_array(config('app.env'), ['local', 'testing'], true) ? 'Pilih Kelas Otomatis' : 'Roster demo tidak tersedia di environment ini' }}</span>
                </label>
                <label id="lbl_admin_type_csv" style="flex: 1; display: flex; align-items: center; gap: 8px; padding: 10px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 8px; cursor: pointer; font-size: 11px; font-weight: 700; color: #64748b;">
                    <input type="radio" name="import_type" value="csv" id="admin_import_type_csv" @checked(! in_array(config('app.env'), ['local', 'testing'], true))>
                    <span>Unggah File CSV</span>
                </label>
            </div>

            <div id="admin_section_class">
                <label>
                    <span>Pilih Kelas yang Didaftarkan:</span>
                    <select name="kelas" id="admin_select_class" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700; color: #17324D;">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($presetClasses as $cls)
                            <option value="{{ $cls }}">{{ $cls }} (Kelas XII)</option>
                        @endforeach
                    </select>
                </label>
                <p style="font-size: 11px; color: #64748b; margin-top: 6px;">
                    @if (! in_array(config('app.env'), ['local', 'testing'], true))
                        Roster contoh dinonaktifkan di environment ini. Gunakan CSV berisi data siswa sekolah.
                    @else
                        Setiap akun baru mendapat password acak unik yang ditampilkan satu kali setelah impor.
                    @endif
                </p>
            </div>

            <div id="admin_section_csv" style="display: none;">
                <label>
                    <span>Unggah Berkas CSV Siswa:</span>
                    <input type="file" name="csv_file" accept=".csv,text/csv" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </label>
                <p style="font-size: 11px; color: #64748b;">Password acak tiap akun baru ditampilkan satu kali setelah impor; siswa wajib menggantinya saat login pertama.</p>
                <div style="margin-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 10px; color: #64748b;">Format: nisn, nama, kelas, jenis_kelamin, riwayat_alergi</span>
                    <a href="{{ route('admin.siswa.template') }}" style="font-size: 11px; font-weight: 700; color: #2E9B67; text-decoration: underline;" download>
                        Unduh Template CSV
                    </a>
                </div>
            </div>

            <div class="siuks-student-form-actions" style="margin-top: 18px;">
                <button type="button" class="btn-close-admin-import">Batal</button>
                <button type="submit" style="background: #2E9B67; color: #fff; font-weight: 700; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer;">
                    ⚡ Import &amp; Daftarkan Semua Siswa
                </button>
            </div>
        </form>
    </dialog>

    <dialog class="siuks-student-dialog" id="admin-student-edit-dialog" data-admin-student-dialog data-kind="edit">
        <form method="POST" class="siuks-student-form" data-admin-student-form data-old-name="{{ old('name') }}" data-old-email="{{ old('email') }}" data-old-nisn="{{ old('nisn') }}" data-old-class="{{ old('kelas') }}" data-old-gender="{{ old('jenis_kelamin') }}" data-old-allergies="{{ old('riwayat_alergi') }}">
            @csrf
            @method('PUT')
            <div class="siuks-student-dialog-heading"><div><p>Kelola data siswa</p><h2>Edit data siswa</h2></div><button type="button" data-close-admin-student-dialog aria-label="Tutup">×</button></div>
            @foreach (['name' => 'Nama lengkap', 'email' => 'Email akun', 'nisn' => 'NISN', 'kelas' => 'Kelas'] as $field => $label)
                <label><span>{{ $label }}</span><input name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" required maxlength="{{ $field === 'nisn' ? 20 : 255 }}">@error($field)<small>{{ $message }}</small>@enderror</label>
            @endforeach
            <label><span>Jenis kelamin</span><select name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select>@error('jenis_kelamin')<small>{{ $message }}</small>@enderror</label>
            <label><span>Riwayat alergi (opsional)</span><textarea name="riwayat_alergi" rows="3" maxlength="2000"></textarea>@error('riwayat_alergi')<small>{{ $message }}</small>@enderror</label>
            <div class="siuks-student-form-actions"><button type="button" data-close-admin-student-dialog>Batal</button><button type="submit">Simpan perubahan</button></div>
        </form>
    </dialog>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const importDialog = document.getElementById('admin-student-import-dialog');
        const openBtn = document.getElementById('btn-open-admin-student-import');
        const closeBtns = document.querySelectorAll('.btn-close-admin-import');

        if (openBtn && importDialog) {
            openBtn.addEventListener('click', function () {
                importDialog.showModal();
            });
        }

        closeBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                if (importDialog) importDialog.close();
            });
        });

        const rClass = document.getElementById('admin_import_type_class');
        const rCsv = document.getElementById('admin_import_type_csv');
        const sClass = document.getElementById('admin_section_class');
        const sCsv = document.getElementById('admin_section_csv');
        const lblClass = document.getElementById('lbl_admin_type_class');
        const lblCsv = document.getElementById('lbl_admin_type_csv');

        function toggleType() {
            if (rClass && rClass.checked) {
                sClass.style.display = 'block';
                sCsv.style.display = 'none';
                lblClass.style.borderColor = '#2E9B67';
                lblClass.style.background = '#fff';
                lblCsv.style.borderColor = '#cbd5e1';
                lblCsv.style.background = '#f8fafc';
            } else if (rCsv && rCsv.checked) {
                sClass.style.display = 'none';
                sCsv.style.display = 'block';
                lblCsv.style.borderColor = '#2E9B67';
                lblCsv.style.background = '#fff';
                lblClass.style.borderColor = '#cbd5e1';
                lblClass.style.background = '#f8fafc';
            }
        }

        if (rClass) rClass.addEventListener('change', toggleType);
        if (rCsv) rCsv.addEventListener('change', toggleType);
        toggleType();
    });
</script>
</body>
</html>
