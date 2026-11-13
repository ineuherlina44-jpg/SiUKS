<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FDE7EA">
    <title>Dashboard Wali Kelas | SiUKS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body wali-dashboard">
    <div class="wali-app">
        <aside class="wali-sidebar">
            <a class="wali-brand" href="#dashboard" aria-label="SiUKS dashboard wali kelas"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""><span><strong>SiUKS</strong><small>Portal Wali Kelas</small></span></a>
            <nav class="wali-navigation" aria-label="Navigasi wali kelas">
                <button type="button" class="wali-nav-item is-active" data-target="dashboard" aria-controls="wali-panel-dashboard" aria-pressed="true"><span aria-hidden="true">⌂</span>Dashboard</button>
                <button type="button" class="wali-nav-item" data-target="kunjungan" aria-controls="wali-panel-kunjungan" aria-pressed="false"><span aria-hidden="true">▤</span>Kunjungan UKS</button>
                <button type="button" class="wali-nav-item" data-target="rekap" aria-controls="wali-panel-rekap" aria-pressed="false"><span aria-hidden="true">▦</span>Rekap kelas</button>
                <a class="wali-nav-item" href="{{ route('wali-kelas.siswa.index') }}"><span aria-hidden="true">♙</span>Data siswa</a>
                <a class="wali-nav-item" href="{{ route('wali-kelas.monitoring') }}"><span aria-hidden="true">◎</span>Monitoring siswa</a>
            </nav>
            <div class="wali-sidebar-foot"><i></i>Kelas dampu <strong>{{ $class }}</strong></div>
        </aside>

        <main class="wali-main" id="dashboard">
            <header class="wali-topbar">
                <button class="wali-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi"><span aria-hidden="true">⋯</span></button>
                <div class="wali-top-context"><span>Portal Wali Kelas</span><strong>Dashboard</strong></div>
                <div class="wali-account"><span class="wali-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><span class="wali-account-name">{{ $user->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="wali-logout" type="submit" title="Logout">Keluar</button></form></div>
            </header>

            <div class="wali-content">
                <section class="wali-view-panel is-active" id="wali-panel-dashboard" data-panel="dashboard">
                    <section class="wali-heading"><div><p>Ringkasan kelas</p><h1>Halo, {{ $user->name }}</h1><span>Pantau kunjungan UKS dan kondisi siswa kelas {{ $class }}.</span></div><span class="wali-class-tag">{{ $class }}</span></section>

                    <section class="wali-overview" aria-label="Ringkasan kelas">
                        <article class="wali-overview-card"><span class="wali-overview-icon icon-blue" aria-hidden="true">◉</span><div><span>Siswa sakit bulan ini</span><strong>{{ $studentsVisitedThisMonth }}</strong><small>kunjungan siswa</small></div></article>
                        <article class="wali-overview-card"><span class="wali-overview-icon icon-red" aria-hidden="true">!</span><div><span>Pemicu SOS bulan ini</span><strong>{{ $sosThisMonth }}</strong><small>laporan darurat</small></div></article>
                        <article class="wali-overview-card"><span class="wali-overview-icon icon-green" aria-hidden="true">✓</span><div><span>Total siswa kelas</span><strong>{{ $totalStudents }}</strong><small>siswa terdaftar</small></div></article>
                    </section>

                    <section class="wali-analytics" aria-label="Grafik dan data kunjungan">
                        <article class="wali-panel wali-chart-panel">
                            <div class="wali-panel-heading"><div><h2>Kunjungan UKS</h2><p>Jumlah kunjungan per bulan · {{ now()->year }}</p></div><span class="wali-panel-key"><i></i>Kunjungan</span></div>
                            <div class="wali-chart" role="img" aria-label="Grafik kunjungan UKS per bulan">
                                @foreach ($chart as $point)
                                    <div class="wali-chart-column"><span class="wali-chart-value">{{ $point['total'] ?: '' }}</span><div class="wali-chart-track"><span style="height: {{ $point['height'] }}%"></span></div><span class="wali-chart-label">{{ $point['label'] }}</span></div>
                                @endforeach
                            </div>
                        </article>

                        <article class="wali-panel wali-class-panel">
                            <div class="wali-panel-heading"><div><h2>Ringkasan kelas</h2><p>Data siswa kelas dampu</p></div></div>
                            <div class="wali-class-summary"><span>Total siswa</span><strong>{{ $totalStudents }}</strong></div>
                            <div class="wali-class-summary"><span>Pernah ke UKS</span><strong>{{ $studentsVisitedThisMonth }}</strong></div>
                            <div class="wali-class-summary"><span>SOS bulan ini</span><strong>{{ $sosThisMonth }}</strong></div>
                            <p class="wali-summary-foot">Penghitungan kunjungan memakai data rekam medis.</p>
                        </article>
                    </section>
                </section>

                <section class="wali-view-panel" id="wali-panel-kunjungan" data-panel="kunjungan">
                    <section class="wali-panel wali-visits-panel" id="kunjungan">
                        <div class="wali-panel-heading"><div><h2>Daftar siswa yang pernah ke UKS</h2><p>Kunjungan terbaru dari kelas {{ $class }}</p></div><a class="wali-download" id="rekap" href="{{ route('wali-kelas.rekap') }}"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v11m0 0 4-4m-4 4-4-4M4 17v3h16v-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>Unduh rekap</a></div>
                        <div class="wali-table-wrap"><table class="wali-table"><thead><tr><th>Nama siswa</th><th>Tanggal</th><th>Keluhan</th><th>Tindakan</th><th>Status</th></tr></thead><tbody>
                            @forelse ($recentVisits as $visit)
                                @php
                                    $statusLabel = match ($visit->status) {
                                        'diizinkan_pulang' => 'Pulang',
                                        'dirawat' => 'Dirawat',
                                        'rujukan' => 'Rujukan',
                                        default => ucfirst($visit->status),
                                    };
                                @endphp
                                <tr><td><strong>{{ $visit->nama_siswa }}</strong><small>NISN {{ $visit->nisn }}</small></td><td>{{ \Illuminate\Support\Carbon::parse($visit->waktu_pemeriksaan)->format('d M Y') }}</td><td>{{ $visit->keluhan ?: '-' }}</td><td>{{ $visit->tindakan ?: '-' }}</td><td><span class="wali-status-pill {{ $visit->status === 'diizinkan_pulang' ? 'status-safe' : 'status-attention' }}">{{ $statusLabel }}</span></td></tr>
                            @empty
                                <tr><td class="wali-empty" colspan="5">Belum ada siswa dari kelas ini yang tercatat berkunjung ke UKS.</td></tr>
                            @endforelse
                        </tbody></table></div>
                    </section>
                </section>

                <section class="wali-view-panel" id="wali-panel-rekap" data-panel="rekap">
                    <section class="wali-panel wali-visits-panel">
                        <div class="wali-panel-heading"><div><h2>Rekap kelas</h2><p>Ringkasan kondisi kesehatan siswa kelas {{ $class }}</p></div><a class="wali-download" href="{{ route('wali-kelas.rekap') }}"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v11m0 0 4-4m-4 4-4-4M4 17v3h16v-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>Unduh rekap</a></div>
                        <div class="wali-class-summary"><span>Total siswa</span><strong>{{ $totalStudents }}</strong></div>
                        <div class="wali-class-summary"><span>Kunjungan bulan ini</span><strong>{{ $studentsVisitedThisMonth }}</strong></div>
                        <div class="wali-class-summary"><span>Darurat / SOS</span><strong>{{ $sosThisMonth }}</strong></div>
                        <div class="wali-class-summary"><span>Status kelas</span><strong>Normal</strong></div>
                        <p class="wali-summary-foot">Rekap ini diambil dari data kunjungan UKS, riwayat rekam medis, dan sinyal SOS siswa.</p>
                    </section>
                </section>
                <footer class="wali-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data kelas {{ $class }}</span></footer>
            </div>
        </main>
    </div>

    <script>
        const waliNavButtons = document.querySelectorAll('.wali-navigation button.wali-nav-item');
        const waliPanels = document.querySelectorAll('.wali-view-panel');

        waliNavButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.dataset.target;

                waliNavButtons.forEach((item) => item.classList.toggle('is-active', item === button));
                waliNavButtons.forEach((item) => item.setAttribute('aria-pressed', String(item === button)));
                waliPanels.forEach((panel) => {
                    panel.classList.toggle('is-active', panel.dataset.panel === target);
                });
            });
        });
    </script>
</body>
</html>