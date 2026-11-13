<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eaf3fc">
    <title>{{ $management ? 'Data Siswa' : 'Monitoring Siswa' }} | SiUKS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body wali-dashboard">
    <div class="wali-app" data-validation-errors="{{ $errors->any() ? 'true' : 'false' }}">
        <aside class="wali-sidebar">
            <a class="wali-brand" href="{{ route('dashboard') }}" aria-label="SiUKS dashboard wali kelas"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""><span><strong>SiUKS</strong><small>Portal Wali Kelas</small></span></a>
            <nav class="wali-navigation" aria-label="Navigasi wali kelas">
                <a class="wali-nav-item" href="{{ route('dashboard') }}"><span aria-hidden="true">⌂</span>Dashboard</a>
                <a class="wali-nav-item {{ $management ? 'is-active' : '' }}" href="{{ route('wali-kelas.siswa.index') }}"><span aria-hidden="true">♙</span>Data siswa</a>
                <a class="wali-nav-item {{ $management ? '' : 'is-active' }}" href="{{ route('wali-kelas.monitoring') }}"><span aria-hidden="true">◎</span>Monitoring siswa</a>
                <a class="wali-nav-item" href="{{ route('dashboard') }}#kunjungan"><span aria-hidden="true">▤</span>Kunjungan UKS</a>
                <a class="wali-nav-item" href="{{ route('wali-kelas.rekap') }}"><span aria-hidden="true">▦</span>Rekap kelas</a>
            </nav>
            <div class="wali-sidebar-foot"><i></i>Kelas dampu <strong>{{ $class }}</strong></div>
        </aside>

        <main class="wali-main">
            <header class="wali-topbar">
                <button class="wali-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi">⋯</button>
                <div class="wali-top-context"><span>Portal Wali Kelas</span><strong>{{ $management ? 'Data Siswa' : 'Monitoring Siswa' }}</strong></div>
                <div class="wali-account"><span class="wali-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><span class="wali-account-name">{{ $user->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="wali-logout" type="submit" title="Keluar">Keluar</button></form></div>
            </header>

            <div class="wali-content">
                <section class="wali-heading">
                    <div><p>{{ $management ? 'Manajemen kelas' : 'Pemantauan kesehatan' }}</p><h1>{{ $management ? 'Data Siswa' : 'Monitoring Siswa' }}</h1><span>{{ $management ? 'Kelola data siswa pada kelas dampu Anda.' : 'Pantau kondisi kesehatan dan kunjungan UKS siswa kelas dampu.' }}</span></div>
                    <span class="wali-class-tag">{{ $class }}</span>
                </section>

                @if (session('status'))
                    <div class="wali-notice wali-notice-success" role="status">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="wali-notice wali-notice-error" role="alert">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="wali-notice wali-notice-error" role="alert">Periksa kembali data yang dimasukkan pada formulir.</div>
                @endif
                @include('partials.student-import-credentials')

                <section class="wali-overview wali-overview-four" aria-label="Ringkasan monitoring siswa">
                    <article class="wali-overview-card"><span class="wali-overview-icon icon-blue" aria-hidden="true">♙</span><div><span>Total siswa kelas</span><strong>{{ $totalStudents }}</strong><small>{{ $class }}</small></div></article>
                    <article class="wali-overview-card"><span class="wali-overview-icon icon-red" aria-hidden="true">!</span><div><span>Siswa sakit bulan terpilih</span><strong>{{ $sickStudents }}</strong><small>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->locale('id')->translatedFormat('F Y') }}</small></div></article>
                    <article class="wali-overview-card"><span class="wali-overview-icon icon-green" aria-hidden="true">✓</span><div><span>Pernah ke UKS</span><strong>{{ $visitedStudents }}</strong><small>sepanjang waktu</small></div></article>
                    <article class="wali-overview-card"><span class="wali-overview-icon icon-red" aria-hidden="true">SOS</span><div><span>SOS bulan terpilih</span><strong>{{ $sosThisMonth }}</strong><small>laporan darurat</small></div></article>
                </section>

                <section class="wali-panel wali-monitoring-panel">
                    <div class="wali-panel-heading wali-monitoring-heading"><div><h2>{{ $management ? 'Daftar siswa' : 'Daftar siswa' }}</h2><p>{{ $management ? 'Data dibatasi ke kelas dampu. Penghapusan siswa dengan riwayat UKS atau SOS tidak diizinkan.' : 'Status sakit dihitung dari kunjungan UKS pada bulan yang dipilih.' }}</p></div>
                        @if ($management)
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button class="wali-add-student" style="background: #2E9B67; color: #fff; border: none; box-shadow: 0 4px 12px rgba(46, 155, 103, 0.25);" type="button" id="btn-open-wali-student-import">📥 Import Siswa Kelas {{ $class }}</button>
                                <button class="wali-add-student" type="button" data-open-student-create>＋ Tambah manual</button>
                            </div>
                        @endif
                    </div>
                    <form class="wali-monitoring-filters" method="GET" action="{{ $management ? route('wali-kelas.siswa.index') : route('wali-kelas.monitoring') }}">
                        <label class="wali-monitoring-field wali-filter-class"><span>Kelas dampu</span><input type="text" value="{{ $class }}" aria-label="Kelas dampu" disabled><small>Akses terbatas ke kelas dampu Anda</small></label>
                        <label class="wali-monitoring-field"><span>Status kesehatan</span><select name="status"><option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Semua status</option><option value="sakit" @selected(($filters['status'] ?? '') === 'sakit')>Sakit</option><option value="sehat" @selected(($filters['status'] ?? '') === 'sehat')>Sehat</option></select></label>
                        <label class="wali-monitoring-field"><span>Bulan</span><input type="month" name="month" value="{{ $month }}"></label>
                        <label class="wali-monitoring-field wali-filter-search"><span>Cari siswa</span><input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama atau NISN"></label>
                        <button class="wali-filter-submit" type="submit">Terapkan</button>
                    </form>

                    @if ($selectedStudent && ! $management)
                        <section class="wali-student-detail" aria-label="Detail kesehatan siswa">
                            <div class="wali-student-detail-heading"><div><p>Detail siswa</p><h3>{{ $selectedStudent->nama_siswa }}</h3><span>NISN {{ $selectedStudent->nisn }} · {{ $selectedStudent->kelas }} · {{ $selectedStudent->jenis_kelamin }}</span></div><a class="wali-detail-close" href="{{ route('wali-kelas.monitoring', collect(request()->query())->except('student')->all()) }}">Tutup</a></div>
                            <p class="wali-student-allergy"><strong>Riwayat alergi:</strong> {{ $selectedStudent->riwayat_alergi ?: 'Belum ada riwayat alergi yang dicatat.' }}</p>
                            <p class="wali-student-allergy"><strong>Laporan SOS pada {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->locale('id')->translatedFormat('F Y') }}:</strong> {{ $selectedStudentSos }}</p>
                            <h4>Riwayat kunjungan UKS</h4>
                            @if ($selectedStudentVisits->isEmpty())
                                <p class="wali-student-no-visits">Belum ada catatan kunjungan UKS.</p>
                            @else
                                <div class="wali-table-wrap"><table class="wali-table"><thead><tr><th>Tanggal</th><th>Diagnosis</th><th>Keluhan</th><th>Tindakan</th><th>Status</th></tr></thead><tbody>
                                    @foreach ($selectedStudentVisits as $visit)
                                        <tr><td>{{ \Illuminate\Support\Carbon::parse($visit->waktu_pemeriksaan)->locale('id')->translatedFormat('d M Y, H:i') }}</td><td>{{ $visit->status_penyakit ?: '-' }}</td><td>{{ $visit->keluhan ?: '-' }}</td><td>{{ $visit->tindakan ?: '-' }}</td><td>{{ ucfirst(str_replace('_', ' ', $visit->status)) }}</td></tr>
                                    @endforeach
                                </tbody></table></div>
                            @endif
                        </section>
                    @endif

                    <div class="wali-table-wrap wali-monitoring-table-wrap"><table class="wali-table wali-monitoring-table"><thead><tr><th>Nama siswa</th><th>NISN</th><th>Kelas</th><th>Jenis kelamin</th><th>Status kesehatan</th><th>{{ $management ? 'Aksi' : 'Terakhir ke UKS' }}</th>@unless ($management)<th>Detail</th>@endunless</tr></thead><tbody>
                        @forelse ($students as $student)
                            <tr><td><strong>{{ $student->nama_siswa }}</strong></td><td>{{ $student->nisn }}</td><td>{{ $student->kelas }}</td><td>{{ $student->jenis_kelamin }}</td><td><span class="wali-status-pill {{ $student->berkunjung_bulan_ini ? 'status-attention' : 'status-safe' }}">{{ $student->berkunjung_bulan_ini ? 'Sakit' : 'Sehat' }}</span></td>
                                @if ($management)
                                    <td class="wali-student-actions"><button class="wali-view-student" type="button" data-edit-student data-id="{{ $student->id }}" data-url="{{ route('wali-kelas.siswa.update', $student->id) }}" data-name="{{ $student->nama_siswa }}" data-nisn="{{ $student->nisn }}" data-gender="{{ $student->jenis_kelamin }}" data-allergies="{{ $student->riwayat_alergi }}">Edit</button><form method="POST" action="{{ route('wali-kelas.siswa.destroy', $student->id) }}" data-delete-student>@csrf @method('DELETE')<button class="wali-delete-student" type="submit">Hapus</button></form></td>
                                @else
                                    <td>{{ $student->kunjungan_terakhir ? \Illuminate\Support\Carbon::parse($student->kunjungan_terakhir)->locale('id')->translatedFormat('d M Y') : 'Belum pernah' }}</td><td><a class="wali-view-student" href="{{ route('wali-kelas.monitoring', array_merge(request()->query(), ['student' => $student->id])) }}">Lihat</a></td>
                                @endif
                            </tr>
                        @empty
                            <tr><td class="wali-empty" colspan="{{ $management ? 6 : 7 }}">Tidak ada siswa yang cocok dengan filter ini.</td></tr>
                        @endforelse
                    </tbody></table></div>
                    @if ($students->hasPages())
                        <div class="wali-monitoring-pagination">{{ $students->links() }}</div>
                    @endif
                </section>
                @if ($management)
                    <dialog class="wali-student-dialog" id="student-create-dialog" data-student-dialog data-kind="create">
                        <form method="POST" action="{{ route('wali-kelas.siswa.store') }}" class="wali-student-form" data-student-form>
                            @csrf
                            <div class="wali-student-dialog-heading"><div><p>Data kelas {{ $class }}</p><h2>Tambah siswa</h2></div><button type="button" class="wali-dialog-close" data-close-student-dialog aria-label="Tutup">×</button></div>
                            @foreach (['name' => 'Nama lengkap', 'email' => 'Email akun', 'nisn' => 'NISN'] as $field => $label)
                                <label class="wali-monitoring-field"><span>{{ $label }}</span><input name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old($field) }}" required maxlength="{{ $field === 'nisn' ? 20 : 255 }}" @if ($field === 'email') autocomplete="email" @endif>@error($field)<small class="wali-field-error">{{ $message }}</small>@enderror</label>
                            @endforeach
                            <label class="wali-monitoring-field"><span>Password awal siswa</span><input name="password" type="password" minlength="8" required autocomplete="new-password">@error('password')<small class="wali-field-error">{{ $message }}</small>@enderror</label>
                            <label class="wali-monitoring-field"><span>Konfirmasi password</span><input name="password_confirmation" type="password" minlength="8" required autocomplete="new-password"></label>
                            <label class="wali-monitoring-field"><span>Jenis kelamin</span><select name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><option value="Laki-laki" @selected(old('jenis_kelamin') === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected(old('jenis_kelamin') === 'Perempuan')>Perempuan</option></select>@error('jenis_kelamin')<small class="wali-field-error">{{ $message }}</small>@enderror</label>
                            <label class="wali-monitoring-field"><span>Riwayat alergi (opsional)</span><textarea name="riwayat_alergi" rows="3" maxlength="2000">{{ old('riwayat_alergi') }}</textarea>@error('riwayat_alergi')<small class="wali-field-error">{{ $message }}</small>@enderror</label>
                            <p class="wali-student-form-note">Kelas otomatis mengikuti kelas dampu Anda. Siswa login menggunakan NISN dan password awal.</p>
                            <div class="wali-student-form-actions"><button type="button" class="wali-detail-close" data-close-student-dialog>Batal</button><button type="submit" class="wali-filter-submit">Simpan siswa</button></div>
                        </form>
                    </dialog>
                    <dialog class="wali-student-dialog" id="student-import-dialog" style="max-width: 500px;">
                        <form method="POST" action="{{ route('wali-kelas.siswa.import') }}" enctype="multipart/form-data" class="wali-student-form">
                            @csrf
                            <div class="wali-student-dialog-heading">
                                <div>
                                    <p style="color: #2E9B67; font-weight: 700;">Data Kelas {{ $class }}</p>
                                    <h2>Import Siswa Otomatis</h2>
                                </div>
                                <button type="button" class="wali-dialog-close btn-close-wali-import" aria-label="Tutup">×</button>
                            </div>

                            <div style="background: #EAF3FF; border: 1px solid rgba(23, 50, 77, 0.12); padding: 12px 14px; border-radius: 10px; margin-bottom: 14px;">
                                <p style="font-size: 11px; color: #17324D; margin: 0; line-height: 1.5;">
                                    💡 <strong>Kelas Dampu: {{ $class }}</strong><br>
                                    Mendaftarkan seluruh siswa kelas <strong>{{ $class }}</strong> secara otomatis lengkap dengan NISN, nama lengkap, jenis kelamin, dan akun login siswa.
                                </p>
                            </div>

                            <div style="display: flex; gap: 8px; margin-bottom: 14px;">
                                <label id="lbl_wali_type_class" style="flex: 1; display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #2E9B67; background: #fff; border-radius: 8px; cursor: pointer; font-size: 11px; font-weight: 700; color: #17324D;">
                                    <input type="radio" name="import_type" value="class" id="wali_import_type_class" @checked(in_array(config('app.env'), ['local', 'testing'], true)) @disabled(! in_array(config('app.env'), ['local', 'testing'], true))>
                                    <span>{{ in_array(config('app.env'), ['local', 'testing'], true) ? 'Otomatis Kelas ' . $class : 'Roster demo tidak tersedia di environment ini' }}</span>
                                </label>
                                <label id="lbl_wali_type_csv" style="flex: 1; display: flex; align-items: center; gap: 8px; padding: 10px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 8px; cursor: pointer; font-size: 11px; font-weight: 700; color: #64748b;">
                                    <input type="radio" name="import_type" value="csv" id="wali_import_type_csv" @checked(! in_array(config('app.env'), ['local', 'testing'], true))>
                                    <span>Unggah File CSV</span>
                                </label>
                            </div>

                            <div id="wali_section_class">
                                <p style="font-size: 11px; color: #475569; margin: 0 0 6px;">
                                    10 - 12 siswa kelas <strong>{{ $class }}</strong> siap diimpor ke sistem secara instan.
                                </p>
                                <p style="font-size: 11px; color: #64748b; margin: 0;">
                                    @if (! in_array(config('app.env'), ['local', 'testing'], true))
                                        Roster contoh dinonaktifkan. Gunakan CSV data siswa sekolah.
                                    @else
                                        Password tiap akun baru acak dan ditampilkan satu kali setelah impor.
                                    @endif
                                </p>
                            </div>

                            <div id="wali_section_csv" style="display: none;">
                                <label class="wali-monitoring-field">
                                    <span>Pilih Berkas CSV Siswa</span>
                                    <input type="file" name="csv_file" accept=".csv,text/csv">
                                </label>
                                <p style="font-size: 11px; color: #64748b;">Password acak tiap akun baru ditampilkan satu kali setelah impor; siswa wajib menggantinya saat login pertama.</p>
                                <div style="margin-top: 6px; display: flex; justify-content: space-between;">
                                    <span style="font-size: 10px; color: #64748b;">Kolom: nisn, nama, jenis_kelamin, riwayat_alergi</span>
                                    <a href="{{ route('wali-kelas.siswa.template') }}" style="font-size: 11px; font-weight: 700; color: #2E9B67; text-decoration: underline;" download>
                                        Unduh Template CSV
                                    </a>
                                </div>
                            </div>

                            <div class="wali-student-form-actions" style="margin-top: 18px;">
                                <button type="button" class="wali-detail-close btn-close-wali-import">Batal</button>
                                <button type="submit" class="wali-filter-submit" style="background: #2E9B67; color: white;">
                                    ⚡ Import Semua Siswa Kelas {{ $class }}
                                </button>
                            </div>
                        </form>
                    </dialog>

                    <dialog class="wali-student-dialog" id="student-edit-dialog" data-student-dialog data-kind="edit">
                        <form method="POST" class="wali-student-form" data-student-form data-old-name="{{ old('name') }}" data-old-nisn="{{ old('nisn') }}" data-old-gender="{{ old('jenis_kelamin') }}" data-old-allergies="{{ old('riwayat_alergi') }}">
                            @csrf
                            @method('PUT')
                            <div class="wali-student-dialog-heading"><div><p>Data kelas {{ $class }}</p><h2>Edit data siswa</h2></div><button type="button" class="wali-dialog-close" data-close-student-dialog aria-label="Tutup">×</button></div>
                            <label class="wali-monitoring-field"><span>Nama lengkap</span><input name="name" type="text" value="{{ old('name') }}" required maxlength="255">@error('name')<small class="wali-field-error">{{ $message }}</small>@enderror</label>
                            <label class="wali-monitoring-field"><span>NISN</span><input name="nisn" type="text" value="{{ old('nisn') }}" required maxlength="20">@error('nisn')<small class="wali-field-error">{{ $message }}</small>@enderror</label>
                            <label class="wali-monitoring-field"><span>Jenis kelamin</span><select name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><option value="Laki-laki" @selected(old('jenis_kelamin') === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected(old('jenis_kelamin') === 'Perempuan')>Perempuan</option></select>@error('jenis_kelamin')<small class="wali-field-error">{{ $message }}</small>@enderror</label>
                            <label class="wali-monitoring-field"><span>Riwayat alergi (opsional)</span><textarea name="riwayat_alergi" rows="3" maxlength="2000">{{ old('riwayat_alergi') }}</textarea>@error('riwayat_alergi')<small class="wali-field-error">{{ $message }}</small>@enderror</label>
                            <div class="wali-student-form-actions"><button type="button" class="wali-detail-close" data-close-student-dialog>Batal</button><button type="submit" class="wali-filter-submit">Simpan perubahan</button></div>
                        </form>
                    </dialog>
                @endif
                <footer class="wali-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data kelas {{ $class }}</span></footer>
            </div>
        </main>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const importDialog = document.getElementById('student-import-dialog');
        const openBtn = document.getElementById('btn-open-wali-student-import');
        const closeBtns = document.querySelectorAll('.btn-close-wali-import');

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

        const rClass = document.getElementById('wali_import_type_class');
        const rCsv = document.getElementById('wali_import_type_csv');
        const sClass = document.getElementById('wali_section_class');
        const sCsv = document.getElementById('wali_section_csv');
        const lblClass = document.getElementById('lbl_wali_type_class');
        const lblCsv = document.getElementById('lbl_wali_type_csv');

        function toggleWaliType() {
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

        if (rClass) rClass.addEventListener('change', toggleWaliType);
        if (rCsv) rCsv.addEventListener('change', toggleWaliType);
        toggleWaliType();
    });
</script>
</body>
</html>
