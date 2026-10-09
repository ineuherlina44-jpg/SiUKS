<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FDE7EA">
    <title>Dashboard Petugas PMR | SiUKS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body pmr-dashboard">
    <div class="pmr-app">
        @include('partials.pmr-sidebar')

        <main class="pmr-main" id="dashboard">
            <header class="pmr-topbar">
                <button class="pmr-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi">⋯</button>
                <label class="pmr-search"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><input type="search" placeholder="Cari nama siswa atau NISN..." aria-label="Cari siswa"></label>
                <div class="pmr-account"><span class="pmr-notification" aria-label="Jumlah sinyal SOS terbaru">{{ $recentAlerts->count() }} SOS</span><span class="pmr-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><span class="pmr-account-name">{{ $user->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="pmr-logout" type="submit">Logout</button></form></div>
            </header>

            <div class="pmr-content">
                <section class="pmr-page-heading"><div><p>Portal Petugas PMR</p><h1>Dashboard</h1><span>Ringkasan layanan UKS dan aktivitas hari ini.</span></div><span class="pmr-today">{{ now()->translatedFormat('l, d F Y') }}</span></section>

                <section class="pmr-overview" aria-label="Ringkasan kondisi UKS">
                    <article class="pmr-overview-card"><span class="pmr-overview-icon overview-red" aria-hidden="true">＋</span><div><span>Pasien hari ini</span><strong>{{ $patientsToday }}</strong><small>siswa berkunjung</small></div></article>
                    <article class="pmr-overview-card"><span class="pmr-overview-icon overview-orange" aria-hidden="true">!</span><div><span>Sedang dirawat</span><strong>{{ $patientsInCare }}</strong><small>status perawatan</small></div></article>
                    <article class="pmr-overview-card"><span class="pmr-overview-icon overview-blue" aria-hidden="true">▧</span><div><span>Stok obat kritis</span><strong>{{ $criticalMedicineCount }}</strong><small>batas stok ≤ {{ $criticalMedicineThreshold }}</small></div></article>
                </section>

                <section class="pmr-actions" aria-label="Aksi cepat">
                    <div class="pmr-action-copy"><span>Aksi cepat</span><strong>Perlu mencatat kunjungan baru?</strong></div>
                    <a class="pmr-action-button is-primary" href="{{ route('pmr.rekam-medis.create') }}"><span aria-hidden="true">＋</span>Catat pasien baru</a>
                    <a class="pmr-action-button is-primary" href="#sinyal-sos"><span aria-hidden="true">!</span>Pantau sinyal SOS</a>
                </section>

                <section class="pmr-panel pmr-visits-panel" id="aktivitas">
                    <div class="pmr-panel-heading"><div><h2>Aktivitas pasien terbaru</h2><p>Kunjungan UKS terbaru</p></div><a href="#stok-obat">Lihat stok kritis</a></div>
                    <div class="pmr-table-wrap"><table class="pmr-table"><thead><tr><th>Pasien</th><th>Waktu</th><th>Keluhan</th><th>Status</th></tr></thead><tbody>
                        @forelse ($recentVisits as $visit)
                            @php
                                $statusLabel = match ($visit->status) {
                                    'diizinkan_pulang' => 'Selesai / pulang',
                                    'dirawat' => 'Sedang dirawat',
                                    'rujukan' => 'Rujukan',
                                    default => ucfirst($visit->status),
                                };
                            @endphp
                            <tr><td><strong>{{ $visit->nama_siswa }}</strong><small>{{ $visit->kelas }}@if ($visit->nama_petugas) · {{ $visit->nama_petugas }}@endif</small></td><td>{{ \Illuminate\Support\Carbon::parse($visit->waktu_pemeriksaan)->format('d M Y') }}</td><td>{{ $visit->keluhan ?: 'Tidak dicatat' }}</td><td><span class="pmr-status-pill {{ $visit->status === 'diizinkan_pulang' ? 'pill-complete' : 'pill-pending' }}">{{ $statusLabel }}</span></td></tr>
                        @empty
                            <tr><td colspan="4" class="pmr-table-empty">Belum ada aktivitas pasien yang tercatat.</td></tr>
                        @endforelse
                    </tbody></table></div>
                </section>

                <section class="pmr-bottom-grid">
                    <article class="pmr-panel pmr-stock-panel" id="stok-obat">
                        <div class="pmr-panel-heading"><div><h2>Obat dengan stok kritis</h2><p>Stok tersedia 5 atau kurang</p></div></div>
                        <ul class="pmr-stock-list">
                            @forelse ($criticalMedicines as $medicine)
                                <li><span><strong>{{ $medicine->nama_obat }}</strong><small>{{ $medicine->satuan }}</small></span><b>{{ $medicine->stok_tersedia }}</b></li>
                            @empty
                                <li class="pmr-empty">Tidak ada obat dengan stok kritis.</li>
                            @endforelse
                        </ul>
                    </article>
                    <article class="pmr-panel pmr-sos-panel" id="sinyal-sos">
                        <div class="pmr-panel-heading"><div><h2>Pantauan sinyal SOS</h2><p>Aktivitas darurat terbaru</p></div><span class="pmr-sos-mark">SOS</span></div>
                        @forelse ($recentAlerts as $alert)
                            <div class="pmr-sos-row"><span class="pmr-sos-indicator" aria-hidden="true">!</span><div><strong>{{ $alert->nama_siswa }} · {{ $alert->kelas }}</strong><small>{{ \Illuminate\Support\Carbon::parse($alert->waktu_pemicu)->format('d M Y, H:i') }}</small></div><span class="pmr-response">{{ $alert->status_respon }}</span></div>
                        @empty
                            <p class="pmr-empty pmr-sos-empty">Belum ada sinyal SOS.</p>
                        @endforelse
                    </article>
                </section>
                <footer class="pmr-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data aktivitas mengikuti catatan UKS</span></footer>
            </div>
        </main>
    </div>
</body>
</html>
