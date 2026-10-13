<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Pasien UKS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen p-6">

    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-md p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Form Pemeriksaan Pasien UKS</h2>
                <p class="text-xs text-slate-500">Pencatatan keluhan, tindakan medis, dan pemberian obat</p>
            </div>
            <a href="{{ route('rekam-medis.index') }}" class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-2 rounded-lg font-semibold">
                &larr; Riwayat Rekam Medis
            </a>
        </div>

        {{-- Notifikasi Error / Peringatan --}}
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 text-xs rounded-xl font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('rekam-medis.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- 1. Pilih Siswa --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Siswa / Pasien:</label>
                <select name="id_siswa" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-medium">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswaList as $s)
                        <option value="{{ $s->id }}" {{ old('id_siswa') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} (Kelas {{ $s->kelas }}) - Alergi: {{ $s->riwayat_alergi ?? 'Tidak Ada' }}
                        </option>
                    @endforeach
                </select>
                @error('id_siswa') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
            </div>

            {{-- 2. Keluhan & Diagnosa Penyakit --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Keluhan Siswa:</label>
                    <input type="text" name="keluhan" value="{{ old('keluhan') }}" placeholder="Contoh: Pusing mual, luka lecet" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs">
                    @error('keluhan') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Diagnosa / Status Penyakit:</label>
                    <input type="text" name="status_penyakit" value="{{ old('status_penyakit') }}" placeholder="Contoh: Sakit Kepala, Dispepsia" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs">
                    @error('status_penyakit') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- 3. Tindakan Penanganan --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tindakan Petugas UKS:</label>
                <textarea name="tindakan" rows="2" placeholder="Contoh: Istirahat di bed UKS selama 20 menit dan kompres dahi" required
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs">{{ old('tindakan') }}</textarea>
                @error('tindakan') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
            </div>

            {{-- 4. S2-04: Bagian Pemberian Obat & Live Badge Stok --}}
            <div class="p-4 bg-teal-50 border border-teal-200 rounded-xl space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-teal-900">Pemberian Obat (Opsional)</span>
                    <span id="stok_info" class="text-[11px] font-semibold text-teal-700 bg-teal-100 px-2 py-0.5 rounded-full">
                        Pilih obat untuk cek stok
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pilih Obat:</label>
                        <select id="select_obat" name="id_obat" class="w-full bg-white border border-teal-300 rounded-lg p-2 text-xs">
                            <option value="" data-stok="0" data-satuan="">-- Tidak Diberi Obat --</option>
                            @foreach($obatList as $o)
                                <option value="{{ $o->id }}" data-stok="{{ $o->stok_tersedia }}" data-satuan="{{ $o->satuan }}">
                                    {{ $o->nama_obat }} (Tersedia: {{ $o->stok_tersedia }} {{ $o->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Dosis (Jumlah):</label>
                        <input type="number" id="input_dosis" name="jumlah_dosis" min="1" placeholder="Dosis"
                               class="w-full bg-white border border-teal-300 rounded-lg p-2 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Aturan Pakai:</label>
                    <input type="text" name="aturan_pakai" placeholder="Contoh: 1 tablet setelah makan"
                           class="w-full bg-white border border-teal-300 rounded-lg p-2 text-xs">
                </div>
            </div>

            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-xl text-xs shadow transition">
                Simpan Pemeriksaan & Selesaikan
            </button>
        </form>
    </div>

    {{-- Script Live Stok (S2-04) --}}
    <script>
        const selectObat = document.getElementById('select_obat');
        const inputDosis = document.getElementById('input_dosis');
        const stokInfo   = document.getElementById('stok_info');

        selectObat.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const stok = parseInt(selected.getAttribute('data-stok') || 0);
            const satuan = selected.getAttribute('data-satuan') || '';

            if (this.value) {
                stokInfo.innerText = `Sisa Stok Apotek: ${stok} ${satuan}`;
                inputDosis.max = stok;
            } else {
                stokInfo.innerText = 'Pilih obat untuk cek stok';
                inputDosis.value = '';
            }
        });
    </script>
</body>
</html>

