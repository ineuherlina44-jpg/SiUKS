<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Rekam Medis UKS | SiUKS</title>
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

    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-xl p-6 md:p-8 border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
            <div>
                <span class="inline-block px-2.5 py-0.5 mb-1 text-[11px] font-bold text-[#D62839] bg-[#FDE7EA] rounded-full">
                    Rekam Medis Digital
                </span>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#17324D]">Riwayat Rekam Medis SiUKS</h1>
                <p class="text-xs text-slate-500 mt-0.5">Daftar pemeriksaan, diagnosa penyakit, dan penanganan kesehatan siswa di UKS</p>
            </div>
            <a href="{{ route('rekam-medis.create') }}" class="inline-flex items-center justify-center gap-1.5 bg-[#D62839] hover:bg-[#ba1f2f] text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-[#D62839]/20 transition active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Periksa Siswa Baru</span>
            </a>
        </div>

        {{-- Pesan Sukses --}}
        @if(session('success'))
            <div class="mb-5 p-3.5 bg-emerald-50 border border-[#2E9B67]/30 text-[#2E9B67] text-xs rounded-xl font-bold flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto rounded-2xl border border-slate-100">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#EAF3FF]/60 border-b border-slate-200 text-[#17324D] font-bold uppercase tracking-wider">
                        <th class="p-3.5">Tanggal</th>
                        <th class="p-3.5">Nama Siswa</th>
                        <th class="p-3.5">Kelas</th>
                        <th class="p-3.5">Keluhan</th>
                        <th class="p-3.5">Diagnosa</th>
                        <th class="p-3.5">Tindakan</th>
                        <th class="p-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($records as $rec)
                        <tr class="hover:bg-[#EAF3FF]/20 transition">
                            <td class="p-3.5 font-mono text-slate-500 whitespace-nowrap">{{ date('d/m/Y H:i', strtotime($rec->waktu_pemeriksaan)) }}</td>
                            <td class="p-3.5 font-bold text-[#17324D]">{{ $rec->nama_siswa }}</td>
                            <td class="p-3.5"><span class="px-2 py-0.5 bg-slate-100 rounded-md font-medium text-slate-600">{{ $rec->kelas }}</span></td>
                            <td class="p-3.5">{{ $rec->keluhan }}</td>
                            <td class="p-3.5">
                                <span class="font-bold text-[#D62839] bg-[#FDE7EA] px-2 py-0.5 rounded-lg inline-block">
                                    {{ $rec->status_penyakit }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600">{{ $rec->tindakan }}</td>
                            <td class="p-3.5 text-center">
                                @if($rec->status === 'dirawat')
                                    <span class="bg-[#F4A340]/15 text-[#F4A340] border border-[#F4A340]/30 font-bold px-2.5 py-1 rounded-full text-[10px]">
                                        Dirawat
                                    </span>
                                @elseif($rec->status === 'rujukan')
                                    <span class="bg-[#D62839]/15 text-[#D62839] border border-[#D62839]/30 font-bold px-2.5 py-1 rounded-full text-[10px]">
                                        Rujukan
                                    </span>
                                @else
                                    <span class="bg-[#2E9B67]/15 text-[#2E9B67] border border-[#2E9B67]/30 font-bold px-2.5 py-1 rounded-full text-[10px]">
                                        Diizinkan Pulang
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Belum ada riwayat rekam medis yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
