<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#EEF4FB">
    <title>Riwayat Rekam Medis | SiUKS</title>
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
                <div class="pmr-topbar-title"><span>Portal Petugas PMR</span><strong>Riwayat rekam medis</strong></div>
                <div class="pmr-account">
                    <span class="pmr-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <span class="pmr-account-name">{{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="pmr-logout" type="submit">Logout</button></form>
                </div>
            </header>

            <div class="pmr-content pmr-history-content">
                <section class="pmr-page-heading">
                    <div><p>Pelayanan UKS</p><h1>Riwayat Rekam Medis</h1><span>Lihat dan telusuri catatan kunjungan siswa.</span></div>
                    <a class="pmr-medicine-add pmr-history-add" href="{{ route('pmr.rekam-medis.create') }}"><span aria-hidden="true">＋</span>Catat pasien</a>
                </section>

                <section class="pmr-panel pmr-history-panel" aria-labelledby="pmr-history-title">
                    <form class="pmr-history-filters" method="GET" action="{{ route('pmr.rekam-medis.index') }}">
                        <div class="pmr-history-search">
                            <label for="history-search">Cari nama, NISN, atau keluhan</label>
                            <input id="history-search" name="search" type="search" value="{{ $search }}" placeholder="Masukkan nama siswa, NISN, atau kata kunci">
                        </div>
                        <div class="pmr-history-filter">
                            <label for="history-class">Kelas</label>
                            <select id="history-class" name="kelas">
                                <option value="">Semua kelas</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class }}" @selected(($filters['kelas'] ?? '') === $class)>{{ $class }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pmr-history-filter">
                            <label for="history-status">Status</label>
                            <select id="history-status" name="status">
                                <option value="">Semua status</option>
                                <option value="dirawat" @selected(($filters['status'] ?? '') === 'dirawat')>Sedang dirawat</option>
                                <option value="diizinkan_pulang" @selected(($filters['status'] ?? '') === 'diizinkan_pulang')>Diizinkan pulang</option>
                                <option value="rujukan" @selected(($filters['status'] ?? '') === 'rujukan')>Rujukan</option>
                            </select>
                        </div>
                        <div class="pmr-history-dates">
                            <div><label for="history-date-from">Dari tanggal</label><input id="history-date-from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}"></div>
                            <span aria-hidden="true">—</span>
                            <div><label for="history-date-to">Sampai tanggal</label><input id="history-date-to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}"></div>
                        </div>
                        <div class="pmr-history-filter-actions">
                            <button class="pmr-history-submit" type="submit"><span aria-hidden="true">⌕</span>Cari</button>
                            @if (count(array_filter($filters)) > 0)
                                <a class="pmr-history-clear" href="{{ route('pmr.rekam-medis.index') }}">Reset filter</a>
                            @endif
                        </div>
                    </form>

                    <div class="pmr-history-table-heading">
                        <div><h2 id="pmr-history-title">Catatan kunjungan</h2><p>{{ $records->total() }} catatan ditemukan</p></div>
                    </div>
                    <div class="pmr-table-wrap">
                        <table class="pmr-history-table">
                            <thead><tr><th scope="col">Tanggal &amp; waktu</th><th scope="col">Nama siswa</th><th scope="col">Kelas</th><th scope="col">Keluhan / diagnosis</th><th scope="col">Penanganan</th><th scope="col">Status</th></tr></thead>
                            <tbody>
                                @forelse ($records as $record)
                                    @php
                                        $statusLabel = match ($record->status) {
                                            'diizinkan_pulang' => 'Diizinkan pulang',
                                            'dirawat' => 'Sedang dirawat',
                                            'rujukan' => 'Rujukan',
                                            default => ucfirst($record->status),
                                        };
                                        $statusClass = match ($record->status) {
                                            'diizinkan_pulang' => 'is-complete',
                                            'dirawat' => 'is-in-care',
                                            'rujukan' => 'is-referral',
                                            default => '',
                                        };
                                    @endphp
                                    <tr>
                                        <td><strong>{{ \Illuminate\Support\Carbon::parse($record->waktu_pemeriksaan)->format('d M Y') }}</strong><small>{{ \Illuminate\Support\Carbon::parse($record->waktu_pemeriksaan)->format('H:i') }}</small></td>
                                        <td><strong>{{ $record->nama_siswa }}</strong><small>NISN {{ $record->nisn }}</small></td>
                                        <td>{{ $record->kelas }}</td>
                                        <td><strong>{{ $record->status_penyakit ?: 'Keluhan' }}</strong><small>{{ \Illuminate\Support\Str::limit($record->keluhan ?: 'Tidak dicatat', 90) }}</small></td>
                                        <td><span class="pmr-history-treatment" title="{{ $record->tindakan }}">{{ \Illuminate\Support\Str::limit($record->tindakan ?: 'Tidak dicatat', 90) }}</span><small>Petugas: {{ $record->nama_petugas ?: '—' }}</small></td>
                                        <td><span class="pmr-history-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="pmr-table-empty">{{ count(array_filter($filters)) > 0 ? 'Tidak ada catatan yang cocok dengan filter.' : 'Belum ada riwayat rekam medis.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($records->hasPages())
                        <div class="pmr-history-pagination">
                            <span>Menampilkan {{ $records->firstItem() }}–{{ $records->lastItem() }} dari {{ $records->total() }} catatan</span>
                            {{ $records->links() }}
                        </div>
                    @endif
                </section>
                <footer class="pmr-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Riwayat kunjungan tersimpan sebagai rekam medis siswa</span></footer>
            </div>
        </main>
    </div>
</body>
</html>
