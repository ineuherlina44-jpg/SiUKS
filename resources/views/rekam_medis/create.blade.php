<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Pasien UKS | SiUKS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        siuksRed: '#D62839',
                        siuksNavy: '#17324D',
                        siuksPink: '#FDE7EA',
                        siuksBlueLight: '#EAF3FF',
                        siuksGreen: '#2E9B67',
                        siuksOrange: '#F4A340',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-[#EAF3FF] via-[#F8FAFC] to-[#FDE7EA] min-h-screen p-4 md:p-8 text-[#17324D]">

    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl p-6 md:p-8 border border-slate-200/80">
        {{-- Header Form --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <div>
                <span class="inline-block px-2.5 py-0.5 mb-1 text-[11px] font-bold text-[#D62839] bg-[#FDE7EA] rounded-full">
                    Portal Petugas UKS / PMR
                </span>
                <h2 class="text-xl md:text-2xl font-extrabold text-[#17324D]">Form Pemeriksaan Pasien UKS</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pencatatan keluhan, diagnosa penyakit, tindakan, dan pemberian obat</p>
            </div>
            <a href="{{ route('rekam-medis.index') }}" class="text-xs bg-[#EAF3FF] hover:bg-[#d6e7ff] text-[#17324D] px-3.5 py-2 rounded-xl font-bold transition shadow-sm">
                &larr; Riwayat Rekam Medis
            </a>
        </div>

        {{-- Notifikasi Error / Peringatan --}}
        @if(session('error'))
            <div class="mb-4 p-3.5 bg-[#FDE7EA] border border-[#D62839]/30 text-[#D62839] text-xs rounded-xl font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        {{-- kode bukti bahwa error handling berhasil menampilkan pesan error ketika validasi gagal, misalnya jika siswa tidak dipilih atau diagnosa tidak diisi. --}}

        @if ($errors->any())
            <div class="mb-4 p-3.5 bg-[#FDE7EA] border border-[#D62839]/30 text-[#D62839] text-xs rounded-xl font-semibold">
                <p class="font-bold mb-1">Periksa kembali data formulir:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('rekam-medis.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- 1. Pilih Siswa --}}
            <div>
                <label class="block text-xs font-bold text-[#17324D] mb-1.5">
                    Nama Siswa / Pasien: <span class="text-[#D62839]">*</span>
                </label>
                <select name="id_siswa" required class="w-full bg-[#EAF3FF]/40 border border-slate-300 rounded-xl p-2.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#D62839]/20 focus:border-[#D62839] transition">
                    <option value="">-- Pilih Siswa yang Diperiksa --</option>
                    @foreach($siswaList as $s)
                        <option value="{{ $s->id }}" {{ old('id_siswa') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} (Kelas {{ $s->kelas }}) - Riwayat Alergi: {{ $s->riwayat_alergi ?? 'Tidak Ada' }}
                        </option>
                    @endforeach
                </select>
                @error('id_siswa') <span class="text-[#D62839] text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- 2. Keluhan & Diagnosa Penyakit (Dropdown + Lainnya) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Keluhan Siswa --}}
                <div>
                    <label class="block text-xs font-bold text-[#17324D] mb-1.5">
                        Keluhan Siswa: <span class="text-[#D62839]">*</span>
                    </label>
                    <input type="text" name="keluhan" value="{{ old('keluhan') }}" placeholder="Contoh: Pusing berputar, nyeri lambung" required
                           class="w-full bg-[#EAF3FF]/40 border border-slate-300 rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#D62839]/20 focus:border-[#D62839] transition">
                    @error('keluhan') <span class="text-[#D62839] text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Status Penyakit / Diagnosa --}}
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

                <div>
                    <label class="block text-xs font-bold text-[#17324D] mb-1.5">
                        Status Penyakit / Diagnosa: <span class="text-[#D62839]">*</span>
                    </label>
                    <select id="select_status_penyakit" name="status_penyakit_select" required
                            class="w-full bg-[#EAF3FF]/40 border border-slate-300 rounded-xl p-2.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#D62839]/20 focus:border-[#D62839] transition">
                        <option value="">-- Pilih Jenis Penyakit --</option>
                        @foreach($daftarPenyakit as $penyakit)
                            <option value="{{ $penyakit }}" @selected($selectedPenyakit === $penyakit)>{{ $penyakit }}</option>
                        @endforeach
                        <option value="Lainnya" @selected($isCustomPenyakit || old('status_penyakit_select') === 'Lainnya')>Lainnya (Ketik Manual...)</option>
                    </select>

                    {{-- Container Input Tambahan Jika Opsi 'Lainnya' Dipilih --}}
                    <div id="wrapper_penyakit_custom" class="{{ ($isCustomPenyakit || old('status_penyakit_select') === 'Lainnya') ? '' : 'hidden' }} mt-2">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="text-[11px] font-bold text-[#17324D]">Diagnosa Khusus / Lainnya:</span>
                            <span class="text-[10px] text-[#D62839] font-medium">* Ketik diagnosa spesifik</span>
                        </div>
                        <input type="text" id="input_penyakit_custom" name="status_penyakit_custom"
                               value="{{ $isCustomPenyakit ? $selectedPenyakit : old('status_penyakit_custom') }}"
                               placeholder="Contoh: Radang Amandel, Hipotensi, Nyeri Sendi..."
                               maxlength="150"
                               {{ ($isCustomPenyakit || old('status_penyakit_select') === 'Lainnya') ? 'required' : '' }}
                               class="w-full bg-white border border-[#D62839]/40 rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#D62839]/20 focus:border-[#D62839] transition">
                    </div>

                    <input type="hidden" id="status_penyakit" name="status_penyakit" value="{{ $selectedPenyakit }}">
                    @error('status_penyakit') <span class="text-[#D62839] text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- 3. Tindakan Penanganan --}}
            <div>
                <label class="block text-xs font-bold text-[#17324D] mb-1.5">
                    Tindakan Petugas UKS: <span class="text-[#D62839]">*</span>
                </label>
                <textarea name="tindakan" rows="2" placeholder="Contoh: Istirahat di bed UKS selama 20 menit, kompres hangat, dan minum air putih" required
                          class="w-full bg-[#EAF3FF]/40 border border-slate-300 rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#D62839]/20 focus:border-[#D62839] transition">{{ old('tindakan') }}</textarea>
                @error('tindakan') <span class="text-[#D62839] text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- 4. Bagian Pemberian Obat & Live Badge Stok (S2-04) --}}
            <div class="p-4 bg-[#EAF3FF]/70 border border-[#17324D]/15 rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#2E9B67]"></span>
                        <span class="text-xs font-bold text-[#17324D]">Pemberian Obat UKS (Opsional)</span>
                    </div>
                    <span id="stok_info" class="text-[11px] font-bold text-[#2E9B67] bg-white border border-[#2E9B67]/30 px-3 py-1 rounded-full shadow-sm">
                        Pilih obat untuk cek stok
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-[#17324D] mb-1">Pilih Obat dari Apotek UKS:</label>
                        <select id="select_obat" name="id_obat" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#2E9B67]/20 focus:border-[#2E9B67] transition">
                            <option value="" data-stok="0" data-satuan="">-- Tidak Diberi Obat / Hanya Istirahat --</option>
                            @foreach($obatList as $o)
                                <option value="{{ $o->id }}" data-stok="{{ $o->stok_tersedia }}" data-satuan="{{ $o->satuan }}">
                                    {{ $o->nama_obat }} (Tersedia: {{ $o->stok_tersedia }} {{ $o->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#17324D] mb-1">Jumlah Dosis:</label>
                        <input type="number" id="input_dosis" name="jumlah_dosis" min="1" placeholder="Misal: 1"
                               class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#2E9B67]/20 focus:border-[#2E9B67] transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#17324D] mb-1">Aturan Pakai / Petunjuk Konsumsi:</label>
                    <input type="text" name="aturan_pakai" placeholder="Contoh: 1 tablet sesudah makan siang jika masih nyeri"
                           class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#2E9B67]/20 focus:border-[#2E9B67] transition">
                </div>
            </div>

            {{-- Tombol Simpan Resmi SiUKS --}}
            <button type="submit" class="w-full bg-[#D62839] hover:bg-[#ba1f2f] text-white font-bold py-3.5 rounded-2xl text-xs md:text-sm shadow-lg shadow-[#D62839]/25 transition active:scale-[0.99] flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Pemeriksaan & Selesaikan</span>
            </button>
        </form>
    </div>

    {{-- Script Diagnosa Dropdown & Live Stok Obat --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Logika Dropdown Diagnosa Penyakit + Pilihan 'Lainnya'
            const selectPenyakit = document.getElementById('select_status_penyakit');
            const wrapperCustom = document.getElementById('wrapper_penyakit_custom');
            const inputCustom = document.getElementById('input_penyakit_custom');
            const hiddenStatus = document.getElementById('status_penyakit');
            const form = selectPenyakit ? selectPenyakit.closest('form') : null;

            function syncStatus() {
                if (!selectPenyakit) return;
                if (selectPenyakit.value === 'Lainnya') {
                    wrapperCustom.classList.remove('hidden');
                    inputCustom.required = true;
                    if (inputCustom.value.trim() !== '') {
                        hiddenStatus.value = inputCustom.value.trim();
                    }
                } else {
                    wrapperCustom.classList.add('hidden');
                    inputCustom.required = false;
                    hiddenStatus.value = selectPenyakit.value;
                }
            }

            if (selectPenyakit) {
                selectPenyakit.addEventListener('change', function () {
                    if (this.value === 'Lainnya') {
                        wrapperCustom.classList.remove('hidden');
                        inputCustom.required = true;
                        inputCustom.focus();
                        hiddenStatus.value = inputCustom.value.trim();
                    } else {
                        wrapperCustom.classList.add('hidden');
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

                if (form) {
                    form.addEventListener('submit', function (e) {
                        if (selectPenyakit.value === 'Lainnya') {
                            hiddenStatus.value = inputCustom.value.trim();
                            if (!hiddenStatus.value) {
                                e.preventDefault();
                                inputCustom.focus();
                                alert('Silakan ketik diagnosa jenis penyakit lainnya.');
                            }
                        } else {
                            hiddenStatus.value = selectPenyakit.value;
                        }
                    });
                }

                syncStatus();
            }

            // 2. Logika Live Check Stok Obat (S2-04)
            const selectObat = document.getElementById('select_obat');
            const inputDosis = document.getElementById('input_dosis');
            const stokInfo   = document.getElementById('stok_info');

            if (selectObat && inputDosis && stokInfo) {
                selectObat.addEventListener('change', function () {
                    const selected = this.options[this.selectedIndex];
                    const stok = parseInt(selected.getAttribute('data-stok') || 0);
                    const satuan = selected.getAttribute('data-satuan') || '';

                    if (this.value) {
                        stokInfo.innerText = `Sisa Stok Apotek: ${stok} ${satuan}`;
                        stokInfo.className = 'text-[11px] font-bold text-[#2E9B67] bg-white border border-[#2E9B67]/30 px-3 py-1 rounded-full shadow-sm';
                        inputDosis.max = stok;
                        if (!inputDosis.value) inputDosis.value = 1;
                    } else {
                        stokInfo.innerText = 'Pilih obat untuk cek stok';
                        stokInfo.className = 'text-[11px] font-semibold text-slate-500 bg-white border border-slate-200 px-3 py-1 rounded-full shadow-sm';
                        inputDosis.value = '';
                        inputDosis.removeAttribute('max');
                    }
                });
            }
        });
    </script>
</body>
</html>
