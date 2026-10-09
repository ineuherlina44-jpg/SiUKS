<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#EEF4FB">
    <title>Catat Pasien Sakit | SiUKS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body pmr-dashboard">
    <div class="pmr-app">
        @include('partials.pmr-sidebar')

        <main class="pmr-main">
            <header class="pmr-topbar">
                <button class="pmr-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi">⋯</button>
                <div class="pmr-topbar-title"><span>Portal Petugas PMR</span><strong>Catat pasien sakit</strong></div>
                <div class="pmr-account">
                    <span class="pmr-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <span class="pmr-account-name">{{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="pmr-logout" type="submit">Logout</button></form>
                </div>
            </header>

            <div class="pmr-content pmr-record-content">
                <section class="pmr-page-heading">
                    <div><p>Pelayanan UKS</p><h1>Catat Pasien Sakit</h1><span>Isi informasi kunjungan dan penanganan siswa.</span></div>
                    <a class="pmr-back-link" href="{{ route('dashboard') }}">← Kembali ke dashboard</a>
                </section>

                @if (session('status'))
                    <p class="pmr-form-success" role="status">{{ session('status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="pmr-form-errors" role="alert">
                        <strong>Periksa kembali data yang diisi.</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form class="pmr-record-form" method="POST" action="{{ route('pmr.rekam-medis.store') }}">
                    @csrf
                    <section class="pmr-form-section" aria-labelledby="pmr-student-section-title">
                        <div class="pmr-form-section-heading">
                            <span class="pmr-form-step">01</span>
                            <div><h2 id="pmr-student-section-title">Identitas dan kunjungan</h2><p>Pilih siswa untuk mengisi identitas serta melihat riwayat alergi.</p></div>
                        </div>
                        <div class="pmr-form-grid">
                            <div class="pmr-field pmr-field-wide">
                                <label for="student_id">Pilih siswa <span aria-hidden="true">*</span></label>
                                <select id="student_id" name="student_id" required>
                                    <option value="">Pilih nama atau NISN siswa</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)
                                            data-name="{{ $student->name }}"
                                            data-nisn="{{ $student->nisn }}"
                                            data-class="{{ $student->kelas }}"
                                            data-gender="{{ $student->jenis_kelamin }}"
                                            data-allergies="{{ $student->riwayat_alergi }}">
                                            {{ $student->name }} · {{ $student->nisn }} · {{ $student->kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($students->isEmpty())<small class="pmr-field-help">Belum ada data siswa. Tambahkan data siswa sebelum mencatat kunjungan.</small>@endif
                            </div>
                            <div class="pmr-field">
                                <label for="student_name">Nama siswa</label>
                                <input id="student_name" type="text" value="" placeholder="Terisi setelah siswa dipilih" readonly>
                            </div>
                            <div class="pmr-field">
                                <label for="student_nisn">NISN</label>
                                <input id="student_nisn" type="text" value="" placeholder="—" readonly>
                            </div>
                            <div class="pmr-field">
                                <label for="student_class">Kelas</label>
                                <input id="student_class" type="text" value="" placeholder="—" readonly>
                            </div>
                            <div class="pmr-field">
                                <label for="student_gender">Jenis kelamin</label>
                                <input id="student_gender" type="text" value="" placeholder="—" readonly>
                            </div>
                            <div class="pmr-field">
                                <label for="waktu_pemeriksaan">Tanggal dan waktu <span aria-hidden="true">*</span></label>
                                <input id="waktu_pemeriksaan" name="waktu_pemeriksaan" type="datetime-local" value="{{ old('waktu_pemeriksaan', now()->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="pmr-field">
                                <label for="status">Status kunjungan <span aria-hidden="true">*</span></label>
                                <select id="status" name="status" required>
                                    <option value="">Pilih status</option>
                                    <option value="dirawat" @selected(old('status') === 'dirawat')>Sedang dirawat</option>
                                    <option value="diizinkan_pulang" @selected(old('status') === 'diizinkan_pulang')>Diizinkan pulang</option>
                                    <option value="rujukan" @selected(old('status') === 'rujukan')>Rujukan</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="pmr-form-section" aria-labelledby="pmr-treatment-section-title">
                        <div class="pmr-form-section-heading">
                            <span class="pmr-form-step">02</span>
                            <div><h2 id="pmr-treatment-section-title">Pemeriksaan dan penanganan</h2><p>Catat kondisi siswa dan tindakan yang diberikan.</p></div>
                        </div>
                        <div class="pmr-form-grid pmr-treatment-grid">
                            <div class="pmr-field">
                                <label for="status_penyakit">Status penyakit / diagnosis <span aria-hidden="true">*</span></label>
                                <input id="status_penyakit" name="status_penyakit" type="text" value="{{ old('status_penyakit') }}" placeholder="Contoh: Demam" maxlength="150" required>
                            </div>
                            <div class="pmr-field pmr-field-textarea">
                                <label for="keluhan">Keluhan / gejala <span aria-hidden="true">*</span></label>
                                <textarea id="keluhan" name="keluhan" rows="5" placeholder="Jelaskan keluhan dan gejala yang dirasakan siswa..." required>{{ old('keluhan') }}</textarea>
                            </div>
                            <div class="pmr-field pmr-field-textarea">
                                <label for="tindakan">Tindakan / penanganan <span aria-hidden="true">*</span></label>
                                <textarea id="tindakan" name="tindakan" rows="5" placeholder="Catat tindakan atau penanganan yang diberikan..." required>{{ old('tindakan') }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section class="pmr-allergy-card" aria-labelledby="pmr-allergy-title">
                        <div class="pmr-allergy-icon" aria-hidden="true">!</div>
                        <div class="pmr-field pmr-field-wide">
                            <label id="pmr-allergy-title" for="student_allergies">Riwayat alergi siswa</label>
                            <textarea id="student_allergies" rows="3" placeholder="Pilih siswa untuk melihat riwayat alergi." readonly></textarea>
                            <small class="pmr-field-help">Informasi alergi berasal dari data profil siswa dan hanya ditampilkan di halaman ini.</small>
                        </div>
                    </section>

                    <div class="pmr-form-actions">
                        <button class="pmr-reset-button" type="reset">Reset formulir</button>
                        <button class="pmr-submit-button" type="submit" @disabled($students->isEmpty())><span aria-hidden="true">✓</span>Simpan catatan</button>
                    </div>
                </form>
                <footer class="pmr-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Catatan tersimpan sebagai rekam medis siswa</span></footer>
            </div>
        </main>
    </div>
</body>
</html>
