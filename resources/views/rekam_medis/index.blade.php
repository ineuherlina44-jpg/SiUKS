@extends('layouts.app')

@content
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Riwayat Rekam Medis UKS</h3>
        <a href="{{ route('rekam-medis.create') }}" class="btn btn-primary">+ Tambah Pemeriksaan Baru</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Keluhan</th>
                            <th>Diagnosa</th>
                            <th>Tindakan</th>
                            <th>Foto Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->waktu_pemeriksaan)->format('d/m/Y H:i') }}</td>
                                <td><strong>{{ $item->nama_siswa }}</strong></td>
                                <td>{{ $item->kelas }}</td>
                                <td>{{ $item->keluhan }}</td>
                                <td><span class="badge bg-warning text-dark">{{ $item->status_penyakit }}</span></td>
                                <td>{{ $item->tindakan }}</td>
                                <td class="text-center">
                                    @if($item->foto_kondisi)
                                        <a href="{{ asset('storage/' . $item->foto_kondisi) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $item->foto_kondisi) }}" alt="Foto Luka" width="60" class="img-thumbnail rounded">
                                        </a>
                                    @else
                                        <span class="text-muted small">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Belum ada data rekam medis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
