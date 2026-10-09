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

                <section class="wali-overview wali-overview-four" aria-label="Ringkasan monitoring siswa">
                    <article class="wali-overview-card"><span class="wali-overview-icon icon-blue" aria-hidden="true">♙</span><div><span>Total siswa kelas</span><strong>{{ $totalStudents }}</strong><small>{{ $class }}</small></div></article>
                    <article class="wali-overview-card"><span class="wali-overview-icon icon-red" aria-hidden="true">!</span><div><span>Siswa sakit bulan terpilih</span><strong>{{ $sickStudents }}</strong><small>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->locale('id')->translatedFormat('F Y') }}</small></div></article>
                    <article class="wali-overview-card"><span class="wali-overview-icon icon-green" aria-hidden="true">✓</span><div><span>Pernah ke UKS</span><strong>{{ $visitedStudents }}</strong><small>sepanjang waktu</small></div></article>
                    <article class="wali-overview-card"><span class="wali-overview-icon icon-red" aria-hidden="true">SOS</span><div><span>SOS bulan terpilih</span><strong>{{ $sosThisMonth }}</strong><small>laporan darurat</small></div></article>
                </section>

                <section class="wali-panel wali-monitoring-panel">
                    <div class="wali-panel-heading wali-monitoring-heading"><div><h2>{{ $management ? 'Daftar siswa' : 'Daftar siswa' }}</h2><p>{{ $management ? 'Data dibatasi ke kelas dampu. Penghapusan siswa dengan riwayat UKS atau SOS tidak diizinkan.' : 'Status sakit dihitung dari kunjungan UKS pada bulan yang dipilih.' }}</p></div>
                        @if ($management)
                            <button class="wali-add-student" type="button" data-open-student-create>＋ Tambah siswa</button>
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
</body>
</html>
