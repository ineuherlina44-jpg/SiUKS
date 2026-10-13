@extends('layouts.app')

@content
<div class="container my-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Form Pemeriksaan & Rekam Medis UKS</h5>
        </div>
        <div class="card-body">
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as$error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Wajib enctype="multipart/form-data" untuk Upload File (S2-05) -->
            <form action="{{ route('rekam-medis.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Pilih Siswa -->
                <div class="mb-3">
                    <label for="id_siswa" class="form-label font-weight-bold">Siswa Pasien <span class="text-danger">*</span></label>
                    <select name="id_siswa" id="id_siswa" class="form-select @error('id_siswa') is-invalid @enderror" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswaList as$siswa)
                            <option value="{{ $siswa->id }}" {{ old('id_siswa') == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->name }} (Kelas: {{ $siswa->kelas }}) - Alergi: {{$siswa->riwayat_alergi ?? 'Tidak Ada' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Keluhan -->
                <div class="mb-3">
                    <label for="keluhan" class="form-label font-weight-bold">Keluhan Siswa <span class="text-danger">*</span></label>
                    <textarea name="keluhan" id="keluhan" class="form-control @error('keluhan') is-invalid @enderror" rows="2" placeholder="Contoh: Pusing, mual setelah olahraga..." required>{{ old('keluhan') }}</textarea>
                </div>

                <!-- Status Penyakit / Diagnosa -->
                <div class="mb-3">
                    <label for="status_penyakit" class="form-label font-weight-bold">Diagnosa / Status Penyakit <span class="text-danger">*</span></label>
                    <input type="text" name="status_penyakit" id="status_penyakit" class="form-control @error('status_penyakit') is-invalid @enderror" value="{{ old('status_penyakit') }}" placeholder="Contoh: Maag ringan / Dehidrasi" required>
                </div>

                <!-- Tindakan Medis -->
                <div class="mb-3">
                    <label for="tindakan" class="form-label font-weight-bold">Tindakan Penanganan <span class="text-danger">*</span></label>
                    <textarea name="tindakan" id="tindakan" class="form-control @error('tindakan') is-invalid @enderror" rows="2" placeholder="Contoh: Istirahat di UKS, dikompres..." required>{{ old('tindakan') }}</textarea>
                </div>

                <!-- Upload Foto Kondisi Siswa (S2-05: Max 2MB) -->
                <div class="mb-3 p-3 bg-light border rounded">
                    <label for="foto_kondisi" class="form-label font-weight-bold">Upload Foto Kondisi/Luka Siswa <small class="text-muted">(Opsional, Maksimal 2MB - JPG/PNG)</small></label>
                    <input type="file" name="foto_kondisi" id="foto_kondisi" class="form-control @error('foto_kondisi') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                </div>

                <hr>
                <h6 class="fw-bold text-secondary">Pemberian Obat (Jika Ada)</h6>

                <!-- Pilih Obat & Live Info Stok (S2-04) -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_obat" class="form-label">Obat</label>
                        <select name="id_obat" id="id_obat" class="form-select">
                            <option value="" data-stok="0">-- Tanpa Obat --</option>
                            @foreach($obatList as$obat)
                                <option value="{{ $obat->id }}" data-stok="{{ $obat->stok_tersedia }}" {{ old('id_obat') == $obat->id ? 'selected' : '' }}>
                                    {{ $obat->nama_obat }} (Tersedia: {{$obat->stok_tersedia }})
                                </option>
                            @endforeach
                        </select>
                        <small id="info-stok" class="form-text text-info fw-bold mt-1 d-block"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jumlah_dosis" class="form-label">Jumlah Dosis / Porsi</label>
                        <input type="number" name="jumlah_dosis" id="jumlah_dosis" class="form-control" min="1" value="{{ old('jumlah_dosis', 1) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="aturan_pakai" class="form-label">Aturan Pakai</label>
                    <input type="text" name="aturan_pakai" id="aturan_pakai" class="form-control" placeholder="Contoh: 3x1 sesudah makan" value="{{ old('aturan_pakai') }}">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('rekam-medis.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-success">Simpan Pemeriksaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Live Check Stok Obat (S2-04) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectObat = document.getElementById('id_obat');
        const infoStok = document.getElementById('info-stok');

        selectObat.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stok = selectedOption.getAttribute('data-stok');
            
            if (this.value && stok !== null) {
                infoStok.textContent = `Sisa stok obat di gudang: ${stok} unit`;
            } else {
                infoStok.textContent = '';
            }
        });
    });
</script>
@endsection