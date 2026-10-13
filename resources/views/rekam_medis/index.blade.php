<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Rekam Medis UKS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-md p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Riwayat Rekam Medis SiUKS</h1>
                <p class="text-xs text-slate-500">Daftar pemeriksaan dan penanganan kesehatan siswa di UKS</p>
            </div>
            <a href="{{ route('rekam-medis.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow">
                + Periksa Siswa Baru
            </a>
        </div>

        {{-- Pesan Notifikasi Sukses --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl font-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel Riwayat Pemeriksaan --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase">
                        <th class="p-3">Tanggal & Waktu</th>
                        <th class="p-3">Nama Siswa</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Keluhan</th>
                        <th class="p-3">Diagnosa</th>
                        <th class="p-3">Tindakan Medis</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-mono text-slate-500">{{ date('d/m/Y H:i', strtotime($rec->waktu_pemeriksaan)) }}</td>
                            <td class="p-3 font-bold text-slate-800">{{ $rec->nama_siswa }}</td>
                            <td class="p-3">{{ $rec->kelas }}</td>
                            <td class="p-3">{{ $rec->keluhan }}</td>
                            <td class="p-3 font-semibold text-teal-700">{{ $rec->status_penyakit }}</td>
                            <td class="p-3">{{ $rec->tindakan }}</td>
                            <td class="p-3 text-center">
                                <span class="bg-emerald-100 text-emerald-700 font-bold px-2.5 py-1 rounded-full text-[10px]">
                                    {{ $rec->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">Belum ada riwayat rekam medis siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>