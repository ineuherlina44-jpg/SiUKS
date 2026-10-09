<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FDE7EA">
    <title>Dashboard Siswa | SiUKS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body student-dashboard">
    <div class="student-app">
        <aside class="student-sidebar">
            <a class="student-brand" href="#ringkasan" aria-label="SiUKS dashboard siswa">
                <img src="{{ asset('images/siuks-logo.png.png') }}" alt="">
                <span><strong>SiUKS</strong><small>Portal Siswa</small></span>
            </a>
            <nav class="student-navigation" aria-label="Navigasi siswa">
                <a class="student-nav-item is-active" href="#ringkasan"><span class="student-nav-icon" aria-hidden="true">⌂</span>Dashboard</a>
                <a class="student-nav-item" href="{{ route('siswa.riwayat-kesehatan') }}"><span class="student-nav-icon" aria-hidden="true">▤</span>Riwayat kesehatan saya</a>
                <a class="student-nav-item" href="{{ route('siswa.stok-obat') }}"><span class="student-nav-icon" aria-hidden="true">＋</span>Cek stok obat</a>
            </nav>
            <div class="student-sidebar-bottom"><span class="student-status-dot"></span>SiUKS aktif</div>
        </aside>

        <main class="student-main" id="ringkasan">
            <header class="student-topbar">
                <div class="student-context"><span>Portal Siswa</span><strong>Ringkasan kesehatan</strong></div>
                <div class="student-account">
                    <button class="student-sidebar-toggle" type="button" data-student-sidebar-toggle aria-expanded="true" aria-label="Sembunyikan navigasi" title="Sembunyikan navigasi">
                        <span aria-hidden="true">⋮</span>
                    </button>
                    <span class="student-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <span class="student-account-name">{{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="student-logout" type="submit" aria-label="Logout" title="Logout">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <div class="student-content">
                <section class="student-welcome">
                    <div><p class="student-kicker">Halo, {{ $user->name }}</p><h1>Jaga kesehatanmu di sekolah.</h1><p>Ringkasan kunjungan UKS dan informasi kesehatanmu.</p></div>
                    <span class="student-class">Kelas {{ $student->kelas }}</span>
                </section>

                @if (session('status'))
                    <p class="student-notice">{{ session('status') }}</p>
                @endif
                @if ($errors->any())
                    <p class="student-notice student-notice-error" role="alert">{{ $errors->first() }}</p>
                @endif

                <section class="student-emergency" aria-labelledby="emergency-title">
                    <div><span class="student-emergency-label">Bantuan cepat</span><h2 id="emergency-title">Butuh bantuan UKS?</h2><p>Sinyal SOS akan tercatat pada pemantauan Petugas PMR. Jika dalam bahaya, segera minta bantuan langsung.</p></div>
                    @if ($openAlert)
                        <button class="student-sos-button" type="button" disabled title="Sinyal SOS masih ditangani">
                            <span aria-hidden="true">!</span>
                            {{ $openAlert->status_respon === 'ditangani' ? 'SOS sedang ditangani' : 'SOS menunggu petugas' }}
                        </button>
                    @else
                        <form method="POST" action="{{ route('siswa.sos.store') }}" data-sos-form>
                            @csrf
                            <button class="student-sos-button" type="submit"><span aria-hidden="true">!</span>Kirim SOS</button>
                        </form>
                    @endif
                </section>

                <section class="student-alert-section" aria-labelledby="alert-title">
                    <div class="student-section-heading"><div><h2 id="alert-title">Aktivitas SOS terbaru</h2><p>Petugas PMR dapat melihat sinyal ini di dashboard setelah memuat ulang halaman.</p></div><span class="student-section-mark" aria-hidden="true">SOS</span></div>
                    @if ($recentAlerts->isNotEmpty())
                        <div class="student-alert-list">
                            @foreach ($recentAlerts as $alert)
                                <article class="student-alert-row"><span class="student-alert-icon" aria-hidden="true">!</span><div class="student-alert-copy"><strong>{{ \Illuminate\Support\Carbon::parse($alert->waktu_pemicu)->format('d M Y, H:i') }}</strong><span>Lokasi belum dicatat</span></div><span class="student-response">{{ $alert->status_respon }}</span></article>
                            @endforeach
                        </div>
                    @else
                        <p class="student-empty student-alert-empty">Belum ada aktivitas SOS.</p>
                    @endif
                </section>

                <section class="student-metrics" aria-label="Ringkasan kunjungan">
                    <article class="student-metric"><span class="student-metric-icon metric-red" aria-hidden="true">＋</span><div><span>Total kunjungan UKS</span><strong>{{ $totalVisits }}</strong></div></article>
                    <article class="student-metric"><span class="student-metric-icon metric-blue" aria-hidden="true">▦</span><div><span>Kunjungan bulan ini</span><strong>{{ $monthlyVisits }}</strong></div></article>
                    <article class="student-metric"><span class="student-metric-icon metric-orange" aria-hidden="true">!</span><div><span>SOS dikirim</span><strong>{{ $totalAlerts }}</strong></div></article>
                </section>

                <div class="student-panels">
                    <section class="student-panel" id="riwayat" aria-labelledby="history-title">
                        <div class="student-section-heading"><div><h2 id="history-title">Riwayat kesehatan saya</h2><p>Catatan kunjungan UKS terbaru</p></div></div>
                        <div class="student-table-wrap">
                            <table class="student-table">
                                <thead><tr><th>Tanggal</th><th>Keluhan</th><th>Obat</th><th>Status</th></tr></thead>
                                <tbody>
                                    @forelse ($recentRecords as $record)
                                        @php
                                            $statusLabel = match ($record->status) {
                                                'diizinkan_pulang' => 'Pulang',
                                                'dirawat' => 'Dirawat',
                                                'rujukan' => 'Rujukan',
                                                default => ucfirst($record->status),
                                            };
                                            $medicines = $recordMedicines->get($record->id, collect())->pluck('nama_obat')->unique()->join(', ');
                                        @endphp
                                        <tr><td>{{ \Illuminate\Support\Carbon::parse($record->waktu_pemeriksaan)->format('d M Y') }}</td><td>{{ $record->keluhan ?: 'Tidak dicatat' }}</td><td>{{ $medicines ?: '-' }}</td><td><span class="student-pill">{{ $statusLabel }}</span></td></tr>
                                    @empty
                                        <tr><td colspan="4" class="student-table-empty">Belum ada riwayat kunjungan UKS.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="student-panel" id="stok-obat" aria-labelledby="stock-title">
                        <div class="student-section-heading"><div><h2 id="stock-title">Cek stok obat</h2><p>Informasi stok UKS</p></div></div>
                        <ul class="student-stock-list">
                            @forelse ($medicineStocks as $medicine)
                                <li><span><strong>{{ $medicine->nama_obat }}</strong><small>{{ $medicine->satuan }}</small></span><b class="{{ $medicine->stok_tersedia <= 5 ? 'stock-low' : 'stock-available' }}">{{ $medicine->stok_tersedia }}</b></li>
                            @empty
                                <li class="student-table-empty">Belum ada data stok obat.</li>
                            @endforelse
                        </ul>
                    </section>
                </div>
                <footer class="student-footer">SiUKS · Informasi kesehatan sekolah <span>Data mengikuti catatan UKS</span></footer>
            </div>
        </main>
    </div>
</body>
</html>