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
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <select id="select_status_penyakit" name="status_penyakit_select" required>
                                        <option value="">-- Pilih Jenis Penyakit / Diagnosis --</option>
                                        @php
                                            $daftarPenyakit = [
                                                'Pusing / Sakit Kepala',
                                                'Maag / Nyeri Lambung (Dispepsia)',
                                                'Demam / Meriang',
                                                'Luka Lecet / Cedera Fisik Ringan',
                                                'Kram Perut / Dismenore (Nyeri Haid)',
                                                'Mual & Muntah',
                                                'Flu / Batuk / Pilek',
                                                'Diare / Gangguan Pencernaan',
                                                'Sesak Napas Ringan / Asma',
                                                'Pingsan / Kelelahan (Syncope)',
                                                'Alergi / Gatal-gatal',
                                                'Mimisan (Epistaksis)',
                                                'Sakit Gigi',
                                            ];
                                            $selectedPenyakit = old('status_penyakit', '');
                                            $isCustomPenyakit = !empty($selectedPenyakit) && !in_array($selectedPenyakit, $daftarPenyakit);
                                        @endphp
                                        @foreach ($daftarPenyakit as $penyakit)
                                            <option value="{{ $penyakit }}" @selected($selectedPenyakit === $penyakit)>{{ $penyakit }}</option>
                                        @endforeach
                                        <option value="Lainnya" @selected($isCustomPenyakit || old('status_penyakit_select') === 'Lainnya')>Lainnya (Ketik Manual...)</option>
                                    </select>

                                    <div id="wrapper_penyakit_custom" style="{{ ($isCustomPenyakit || old('status_penyakit_select') === 'Lainnya') ? '' : 'display: none;' }}">
                                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                            <span style="font-size: 11px; font-weight: 700; color: #17324D;">Diagnosa Lainnya / Khusus:</span>
                                            <span style="font-size: 10px; color: #D62839; font-weight: 600;">* Ketik secara spesifik</span>
                                        </div>
                                        <input id="input_penyakit_custom" name="status_penyakit_custom" type="text"
                                               value="{{ $isCustomPenyakit ? $selectedPenyakit : old('status_penyakit_custom') }}"
                                               placeholder="Contoh: Radang Amandel, Hipotensi, Nyeri Sendi..."
                                               maxlength="150"
                                               {{ ($isCustomPenyakit || old('status_penyakit_select') === 'Lainnya') ? 'required' : '' }}>
                                    </div>
                                    <input type="hidden" id="status_penyakit" name="status_penyakit" value="{{ $selectedPenyakit }}">
                                </div>
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

                    <section class="pmr-form-section" aria-labelledby="pmr-medicine-section-title">
                        <div class="pmr-form-section-heading">
                            <span class="pmr-form-step">03</span>
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                    <div>
                                        <h2 id="pmr-medicine-section-title">Pemberian obat UKS</h2>
                                        <p>Pilih obat dari stok apotek UKS serta aturan pakainya (opsional).</p>
                                    </div>
                                    <span id="stok_info_pmr" style="font-size: 11px; font-weight: 700; color: #2E9B67; background: #EAF3FF; border: 1px solid rgba(46, 155, 103, 0.25); padding: 5px 12px; border-radius: 20px;">
                                        Pilih obat untuk cek sisa stok
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="pmr-form-grid">
                            <div class="pmr-field" style="grid-column: span 2;">
                                <label for="id_obat">Pilih obat apotek UKS</label>
                                <select id="id_obat" name="id_obat">
                                    <option value="" data-stok="0" data-satuan="">-- Tidak Diberi Obat (Hanya Istirahat / Penanganan Fisik) --</option>
                                    @foreach ($medicines as $med)
                                        <option value="{{ $med->id }}" @selected((string) old('id_obat') === (string) $med->id) data-stok="{{ $med->stok_tersedia }}" data-satuan="{{ $med->satuan }}">
                                            {{ $med->nama_obat }} (Tersedia: {{ $med->stok_tersedia }} {{ $med->satuan }}) - [{{ $med->kategori }}]
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_obat') <small style="color: #D62839; font-size: 11px;">{{ $message }}</small> @enderror
                            </div>
                            <div class="pmr-field">
                                <label for="jumlah_dosis">Jumlah / dosis</label>
                                <input id="jumlah_dosis" name="jumlah_dosis" type="number" min="1" value="{{ old('jumlah_dosis') }}" placeholder="Misal: 1">
                                @error('jumlah_dosis') <small style="color: #D62839; font-size: 11px;">{{ $message }}</small> @enderror
                            </div>
                            <div class="pmr-field">
                                <label for="aturan_pakai">Aturan pakai / anjuran</label>
                                <input id="aturan_pakai" name="aturan_pakai" type="text" value="{{ old('aturan_pakai') }}" placeholder="Contoh: 1 tablet setelah makan" maxlength="255">
                                @error('aturan_pakai') <small style="color: #D62839; font-size: 11px;">{{ $message }}</small> @enderror
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Logika Diagnosa Penyakit (Dropdown & Custom Input)
            const selectPenyakit = document.getElementById('select_status_penyakit');
            const wrapperCustom = document.getElementById('wrapper_penyakit_custom');
            const inputCustom = document.getElementById('input_penyakit_custom');
            const hiddenStatus = document.getElementById('status_penyakit');
            const form = selectPenyakit ? selectPenyakit.closest('form') : null;

            function syncStatus() {
                if (!selectPenyakit) return;
                if (selectPenyakit.value === 'Lainnya') {
                    wrapperCustom.style.display = 'block';
                    inputCustom.required = true;
                    if (inputCustom.value.trim() !== '') {
                        hiddenStatus.value = inputCustom.value.trim();
                    }
                } else {
                    wrapperCustom.style.display = 'none';
                    inputCustom.required = false;
                    hiddenStatus.value = selectPenyakit.value;
                }
            }

            if (selectPenyakit) {
                selectPenyakit.addEventListener('change', function () {
                    if (this.value === 'Lainnya') {
                        wrapperCustom.style.display = 'block';
                        inputCustom.required = true;
                        inputCustom.focus();
                        hiddenStatus.value = inputCustom.value.trim();
                    } else {
                        wrapperCustom.style.display = 'none';
                        inputCustom.required = false;
                        hiddenStatus.value = this.value;
                    }
                });

                if (inputCustom) {
                    inputCustom.addEventListener('input', function () {
                        if (selectPenyakit.value === 'Lainnya') {
                            hiddenStatus.value = this.value.trim();
                        }
                    });
                }
            }

            // 2. Logika Pemilihan Obat & Live Info Stok Apotek
            const selectObat = document.getElementById('id_obat');
            const inputDosis = document.getElementById('jumlah_dosis');
            const stokInfo = document.getElementById('stok_info_pmr');

            function updateObatInfo() {
                if (!selectObat || !stokInfo) return;
                const selected = selectObat.options[selectObat.selectedIndex];
                const stok = parseInt(selected ? selected.getAttribute('data-stok') : 0) || 0;
                const satuan = (selected ? selected.getAttribute('data-satuan') : '') || '';

                if (selectObat.value) {
                    stokInfo.innerText = `Sisa Stok Apotek: ${stok} ${satuan}`;
                    stokInfo.style.color = '#2E9B67';
                    stokInfo.style.background = '#EAF3FF';
                    stokInfo.style.borderColor = 'rgba(46, 155, 103, 0.4)';
                    if (inputDosis) {
                        inputDosis.max = stok;
                        if (!inputDosis.value || parseInt(inputDosis.value) < 1) {
                            inputDosis.value = 1;
                        }
                    }
                } else {
                    stokInfo.innerText = 'Pilih obat untuk cek sisa stok';
                    stokInfo.style.color = '#17324D';
                    stokInfo.style.background = '#EAF3FF';
                    stokInfo.style.borderColor = 'rgba(23, 50, 77, 0.15)';
                    if (inputDosis) {
                        inputDosis.value = '';
                        inputDosis.removeAttribute('max');
                    }
                }
            }

            if (selectObat) {
                selectObat.addEventListener('change', updateObatInfo);
                updateObatInfo();
            }

            // 3. Form Submit & Reset Event Listeners
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (selectPenyakit && selectPenyakit.value === 'Lainnya') {
                        hiddenStatus.value = inputCustom.value.trim();
                        if (!hiddenStatus.value) {
                            e.preventDefault();
                            inputCustom.focus();
                            alert('Silakan ketik jenis diagnosis penyakit secara spesifik.');
                            return;
                        }
                    } else if (selectPenyakit) {
                        hiddenStatus.value = selectPenyakit.value;
                    }

                    if (selectObat && selectObat.value && inputDosis) {
                        const dosisVal = parseInt(inputDosis.value) || 0;
                        const selected = selectObat.options[selectObat.selectedIndex];
                        const stok = parseInt(selected ? selected.getAttribute('data-stok') : 0) || 0;
                        if (dosisVal < 1) {
                            e.preventDefault();
                            inputDosis.focus();
                            alert('Silakan tentukan jumlah dosis obat yang diberikan (minimal 1).');
                            return;
                        }
                        if (dosisVal > stok) {
                            e.preventDefault();
                            inputDosis.focus();
                            alert(`Jumlah dosis (${dosisVal}) melebihi sisa stok apotek yang tersedia (${stok}).`);
                            return;
                        }
                    }
                });

                form.addEventListener('reset', function () {
                    setTimeout(function () {
                        if (wrapperCustom) wrapperCustom.style.display = 'none';
                        if (inputCustom) inputCustom.required = false;
                        if (hiddenStatus) hiddenStatus.value = '';
                        if (selectObat) updateObatInfo();
                    }, 50);
                });
            }

            syncStatus();
        });
    </script>
</body>
</html>