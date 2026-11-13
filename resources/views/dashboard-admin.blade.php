<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fff3f3">
    <title>Dashboard Admin — SiUKS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        .siuks-view-panel:not([data-panel="dashboard"]) { display: none; }
    </style>
</head>
<body class="siuks-body">
<div class="siuks-app">
    <aside class="siuks-sidebar">
        <a class="siuks-brand" href="#dashboard" aria-label="SiUKS, dashboard"><span class="siuks-brand-icon"><img src="{{ asset('images/siuks-logo.png.png') }}" alt="" class="siuks-brand-image"></span><span class="siuks-brand-text"><strong>SiUKS</strong><small>Sistem Informasi Kesehatan Sekolah</small></span></a>
        <nav class="siuks-navigation" aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}" class="siuks-nav-item is-active" aria-current="page"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z"/></svg><span>Dashboard</span></a>
            <a href="{{ route('admin.siswa.index') }}" class="siuks-nav-item"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20v-1.3A5.7 5.7 0 0 1 8.2 13h1.6a5.7 5.7 0 0 1 5.7 5.7V20H2.5ZM17 5a3.2 3.2 0 0 1 0 6.2M17 14a4.7 4.7 0 0 1 4.5 4.7V20h-4"/></svg><span>Data Siswa</span></a>
            <a href="{{ route('admin.pmr.index') }}" class="siuks-nav-item"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7.5" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg><span>Data Petugas PMR</span></a>
            <a href="{{ route('admin.wali-kelas.index') }}" class="siuks-nav-item"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20v-1a6.5 6.5 0 0 1 13 0v1h-13ZM15 14a5 5 0 0 1 6.5 4.8V20H17"/></svg><span>Data Wali Kelas</span></a>
            <a href="{{ route('admin.obat.index') }}" class="siuks-nav-item"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.1 3.5a5.5 5.5 0 0 1 7.8 0l4.6 4.6a5.5 5.5 0 0 1-7.8 7.8l-4.6-4.6a5.5 5.5 0 0 1 0-7.8Z"/><path d="m9.2 12 5.6-5.6" stroke="#fff" stroke-width="1.6"/></svg><span>Master Obat</span></a>
            <a href="{{ route('admin.rekam-medis.index') }}" class="siuks-nav-item"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Rekam Medis</span></a>
            <a href="{{ route('admin.sos.index') }}" class="siuks-nav-item"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 22 20H2L12 3Z"/><path d="M12 9v5m0 3h.01" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg><span>Monitoring SOS</span></a>
            <a href="{{ route('admin.laporan.index') }}" class="siuks-nav-item"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Laporan</span></a>
        </nav>
        <div class="siuks-sidebar-foot"><span class="siuks-sidebar-status"></span>Sistem UKS Sekolah<small>v1.0</small></div>
    </aside>

    <main class="siuks-main" id="dashboard">
        <header class="siuks-topbar">
            <label class="siuks-search"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><input type="search" placeholder="Cari data siswa, pengguna, atau laporan..." aria-label="Cari data"><kbd>⌘ K</kbd></label>
            <div class="siuks-top-actions"><button class="siuks-notification" type="button" aria-label="Notifikasi"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg><i>3</i></button><div class="siuks-admin"><span class="siuks-admin-avatar"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="4" fill="currentColor"/><path d="M4 21a8 8 0 0 1 16 0H4Z" fill="currentColor"/></svg></span><strong>{{ auth()->user()->name }}</strong><span>⌄</span></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="siuks-logout" type="submit"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Logout</span></button></form></div>
        </header>
        <div class="siuks-content">
            <section class="siuks-view-panel" data-panel="dashboard">
                <section class="siuks-heading">
                    <div>
                        <h1>Dashboard Admin</h1>
                        <p>Kelola seluruh data dan pantau kesehatan sekolah</p>
                    </div>
                    <div class="siuks-date">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M16 3v4M8 3v4M3 10h18M9 14h2v2H9z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Sabtu, 26 September 2026
                    </div>
                </section>

                <section class="siuks-stats-grid" aria-label="Ringkasan data sekolah">
                    <article class="siuks-stat"><span class="siuks-stat-icon siuks-blue"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-1a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM1 21v-2a8 8 0 0 1 16 0v2H1Zm17-7a6 6 0 0 1 5 5.9V21h-4v-2a9.9 9.9 0 0 0-1-5Z"/></svg></span><div><span>Total Siswa</span><strong>520</strong></div></article>
                    <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg></span><div><span>Petugas PMR</span><strong>18</strong></div></article>
                    <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg></span><div><span>Wali Kelas</span><strong>24</strong></div></article>
                    <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.1 3.5a5.5 5.5 0 0 1 7.8 0l4.6 4.6a5.5 5.5 0 0 1-7.8 7.8l-4.6-4.6a5.5 5.5 0 0 1 0-7.8Z"/><path d="m9.2 12 5.6-5.6" stroke="#fff" stroke-width="1.6"/></svg></span><div><span>Jenis Obat</span><strong>35</strong></div></article>
                </section>

                <section class="siuks-summary-grid" aria-label="Ringkasan bulanan">
                    <article class="siuks-summary-card"><span class="siuks-summary-icon siuks-blue"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 3h8v4h4v14H4V7h4V3Zm2 2v4h4V5h-4Zm1 5v3H8v2h3v3h2v-3h3v-2h-3v-3h-2Z"/></svg></span><div><span>Kunjungan UKS Bulan Ini</span><strong>87</strong></div></article>
                    <article class="siuks-summary-card"><span class="siuks-summary-icon siuks-red"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10 2h4v2h-4zM9 5h6v2l2 2v2l3 3v7H4v-7l3-3V9l2-2V5Z"/><path d="M11 10h2v5h-2zm0 6h2v2h-2z" fill="#fff"/></svg></span><div><span>SOS Bulan Ini</span><strong>6</strong></div></article>
                </section>

                <section class="siuks-charts-grid">
                    <article class="siuks-panel"><h2>Jumlah Kunjungan UKS per Bulan</h2><div class="siuks-line-chart" role="img" aria-label="Grafik kunjungan UKS bulanan"><div class="siuks-y-labels"><span>100</span><span>80</span><span>60</span><span>40</span><span>20</span></div><div class="siuks-chart-body"><div class="siuks-chart-grid"><i></i><i></i><i></i><i></i><i></i></div><svg viewBox="0 0 600 190" preserveAspectRatio="none" aria-hidden="true"><path d="M0 150 L55 126 L109 104 L164 124 L218 104 L273 108 L327 91 L382 105 L436 116 L491 108 L545 92 L600 52" fill="none" stroke="#287bd2" stroke-width="3" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"/><g fill="#287bd2" stroke="#fff" stroke-width="2" vector-effect="non-scaling-stroke"><circle cx="0" cy="150" r="4"/><circle cx="55" cy="126" r="4"/><circle cx="109" cy="104" r="4"/><circle cx="164" cy="124" r="4"/><circle cx="218" cy="104" r="4"/><circle cx="273" cy="108" r="4"/><circle cx="327" cy="91" r="4"/><circle cx="382" cy="105" r="4"/><circle cx="436" cy="116" r="4"/><circle cx="491" cy="108" r="4"/><circle cx="545" cy="92" r="4"/><circle cx="600" cy="52" r="4"/></g></svg><div class="siuks-x-labels"><span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span><span>Jul</span><span>Agu</span><span>Sep</span><span>Okt</span><span>Nov</span><span>Des</span></div></div></div></article>
                    <article class="siuks-panel"><h2>Jenis Penyakit Terbanyak</h2><div class="siuks-donut-wrap"><div class="siuks-donut" role="img" aria-label="Diagram jenis penyakit terbanyak"><div><span>Total</span><strong>87</strong></div></div><ul class="siuks-legend"><li><i class="dot-blue"></i><span>Maag</span><b>28%</b></li><li><i class="dot-cyan"></i><span>Pusing</span><b>22%</b></li><li><i class="dot-red"></i><span>Demam</span><b>18%</b></li><li><i class="dot-yellow"></i><span>Batuk</span><b>12%</b></li><li><i class="dot-purple"></i><span>Sakit Perut</span><b>10%</b></li><li><i class="dot-gray"></i><span>Lainnya</span><b>10%</b></li></ul></div></article>
                </section>

                <section class="siuks-bottom-grid">
                    <article class="siuks-panel siuks-activity-panel" id="monitoring-sos"><h2>Aktivitas SOS Terbaru</h2><div class="siuks-table-scroll"><table><thead><tr><th>Waktu</th><th>Siswa</th><th>Kelas</th><th>Lokasi</th><th>Status</th></tr></thead><tbody><tr><td>09:20</td><td>Andi Pratama</td><td>X-1</td><td>Kelas X-1</td><td><span class="siuks-pill pill-handled">Ditangani</span></td></tr><tr><td>10:15</td><td>Siti Aisyah</td><td>XI-2</td><td>Lapangan</td><td><span class="siuks-pill pill-done">Selesai</span></td></tr><tr><td>11:30</td><td>Rina Angustina</td><td>X-3</td><td>Kelas X-3</td><td><span class="siuks-pill pill-done">Selesai</span></td></tr></tbody></table></div></article>
                    <article class="siuks-panel siuks-quick-panel" id="laporan"><h2>Aksi Cepat</h2><div class="siuks-quick-grid"><a href="{{ route('admin.laporan.download') }}"><span class="quick-blue"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 3h9l4 4v14H6V3Z" stroke="currentColor" stroke-width="1.7"/><path d="M14 3v5h5M8 14h8m-8 3h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>Unduh Laporan PDF</a><a href="{{ route('admin.siswa.index') }}"><span class="quick-blue"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M3 20v-1a6 6 0 0 1 12 0v1H3Zm15-10v6m-3-3h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>Kelola Data Pengguna</a><a href="{{ route('admin.obat.index') }}"><span class="quick-blue"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8.1 3.5a5.5 5.5 0 0 1 7.8 0l4.6 4.6a5.5 5.5 0 0 1-7.8 7.8l-4.6-4.6a5.5 5.5 0 0 1 0-7.8Z" stroke="currentColor" stroke-width="1.7"/><path d="m9.2 12 5.6-5.6" stroke="currentColor" stroke-width="1.7"/></svg></span>Kelola Data Obat</a><a href="{{ route('admin.rekam-medis.index') }}"><span class="quick-blue"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z" stroke="currentColor" stroke-width="1.7"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>Lihat Rekam Medis</a><a class="quick-sos" href="{{ route('admin.sos.index') }}"><span class="quick-red"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10 2h4v2h-4zM9 5h6v2l2 2v2l3 3v7H4v-7l3-3V9l2-2V5Z"/><path d="M11 10h2v5h-2zm0 6h2v2h-2z" fill="#fff"/></svg></span>Monitoring SOS</a></div></article>
                </section>

                <footer class="siuks-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data pada dashboard ini merupakan contoh prototipe</span></footer>
            </section>

            <section class="siuks-view-panel is-active" data-panel="siswa">
                <div class="siuks-detail-header">
                    <div>
                        <p class="siuks-page-kicker">Manajemen</p>
                        <h2>Data Siswa</h2>
                    </div>
                    <div class="siuks-detail-actions">
                        <button class="siuks-muted-btn" type="button">Import Excel</button>
                        <button class="siuks-primary-btn" type="button">+ Tambah siswa</button>
                    </div>
                </div>

                <div class="siuks-detail-cards">
                    <article class="siuks-mini-card"><span>Total siswa</span><strong>520</strong><small>+12 bulan ini</small></article>
                    <article class="siuks-mini-card"><span>Aktif</span><strong>498</strong><small>88.4%</small></article>
                    <article class="siuks-mini-card"><span>Perlu perhatian</span><strong>17</strong><small>3.2%</small></article>
                    <article class="siuks-mini-card"><span>Belum lengkap</span><strong>5</strong><small>0.9%</small></article>
                </div>

                <div class="siuks-detail-workspace">
                    <aside class="siuks-side-filter">
                        <h3>Filter data</h3>
                        <div class="siuks-filter-group">
                            <label>Kelas</label>
                            <select><option>Semua kelas</option><option>X-1</option><option>X-2</option><option>XI-1</option></select>
                        </div>
                        <div class="siuks-filter-group">
                            <label>Jurusan</label>
                            <select><option>Semua jurusan</option><option>IPA</option><option>IPS</option><option>Bahasa</option></select>
                        </div>
                        <div class="siuks-filter-group">
                            <label>Status</label>
                            <select><option>Semua</option><option>Aktif</option><option>Perhatian</option><option>Belum lengkap</option></select>
                        </div>
                        <button class="siuks-primary-btn siuks-full-btn" type="button">Terapkan filter</button>
                    </aside>

                    <div class="siuks-record-panel">
                        <div class="siuks-record-toolbar">
                            <div class="siuks-text-search">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <input type="text" value="Cari nama, NIS, kelas" aria-label="Cari siswa">
                            </div>
                            <button class="siuks-mini-button" type="button">Export</button>
                        </div>

                        <div class="siuks-admin-table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>Rina Putri</td><td>2026001</td><td>X-1</td><td>IPA</td><td><span class="siuks-status ok">Aktif</span></td><td><button class="siuks-mini-button" type="button">Detail</button></td></tr>
                                    <tr><td>Fikri Ramadhan</td><td>2026002</td><td>XI-2</td><td>IPS</td><td><span class="siuks-status warn">Perhatian</span></td><td><button class="siuks-mini-button" type="button">Detail</button></td></tr>
                                    <tr><td>Salsa Nabila</td><td>2026003</td><td>X-3</td><td>Bahasa</td><td><span class="siuks-status ok">Aktif</span></td><td><button class="siuks-mini-button" type="button">Detail</button></td></tr>
                                    <tr><td>Andi Putra</td><td>2026004</td><td>XI-1</td><td>IPA</td><td><span class="siuks-status danger">Cek</span></td><td><button class="siuks-mini-button" type="button">Detail</button></td></tr>
                                    <tr><td>Putri Maharani</td><td>2026005</td><td>X-2</td><td>IPS</td><td><span class="siuks-status ok">Aktif</span></td><td><button class="siuks-mini-button" type="button">Detail</button></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section class="siuks-view-panel" data-panel="petugas">
                <div class="siuks-detail-header">
                    <div>
                        <p class="siuks-page-kicker">Petugas</p>
                        <h2>Data Petugas PMR</h2>
                    </div>
                    <div class="siuks-detail-actions">
                        <button class="siuks-muted-btn" type="button">Unduh daftar</button>
                        <button class="siuks-primary-btn" type="button">+ Tambah petugas</button>
                    </div>
                </div>

                <div class="siuks-detail-cards">
                    <article class="siuks-mini-card"><span>Petugas aktif</span><strong>18</strong><small>100%</small></article>
                    <article class="siuks-mini-card"><span>Koordinator</span><strong>4</strong><small>22%</small></article>
                    <article class="siuks-mini-card"><span>Siaga hari ini</span><strong>9</strong><small>50%</small></article>
                    <article class="siuks-mini-card"><span>Jadwal libur</span><strong>2</strong><small>11%</small></article>
                </div>

                <div class="siuks-record-panel">
                    <div class="siuks-record-toolbar">
                        <div class="siuks-text-search">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <input type="text" value="Cari nama atau jabatan" aria-label="Cari petugas">
                        </div>
                        <button class="siuks-mini-button" type="button">Refresh</button>
                    </div>
                    <div class="siuks-admin-table-wrap">
                        <table>
                            <thead><tr><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Kelas</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <tr><td>Putri Lestari</td><td>PMR-01</td><td>Petugas</td><td>XI-3</td><td><span class="siuks-status ok">Aktif</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                                <tr><td>Ahmad Yusuf</td><td>PMR-02</td><td>Koordinator</td><td>XI-1</td><td><span class="siuks-status warn">Standby</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                                <tr><td>Rizky Maulana</td><td>PMR-03</td><td>Petugas</td><td>X-2</td><td><span class="siuks-status ok">Aktif</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="siuks-view-panel" data-panel="wali-kelas">
                <div class="siuks-detail-header">
                    <div>
                        <p class="siuks-page-kicker">Wali</p>
                        <h2>Data Wali Kelas</h2>
                    </div>
                    <div class="siuks-detail-actions">
                        <button class="siuks-muted-btn" type="button">Unduh data</button>
                        <button class="siuks-primary-btn" type="button">+ Tambah wali kelas</button>
                    </div>
                </div>

                <div class="siuks-detail-cards">
                    <article class="siuks-mini-card"><span>Wali aktif</span><strong>24</strong><small>100%</small></article>
                    <article class="siuks-mini-card"><span>Kelas terpantau</span><strong>24</strong><small>100%</small></article>
                    <article class="siuks-mini-card"><span>Review</span><strong>2</strong><small>8.3%</small></article>
                    <article class="siuks-mini-card"><span>Belum valid</span><strong>0</strong><small>0%</small></article>
                </div>

                <div class="siuks-record-panel">
                    <div class="siuks-record-toolbar">
                        <div class="siuks-text-search">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <input type="text" value="Cari nama atau kelas" aria-label="Cari wali kelas">
                        </div>
                        <button class="siuks-mini-button" type="button">Filter</button>
                    </div>
                    <div class="siuks-admin-table-wrap">
                        <table>
                            <thead><tr><th>Nama</th><th>NIP</th><th>Kelas</th><th>Jurusan</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <tr><td>Bu Meli</td><td>WK-101</td><td>X-1</td><td>IPA</td><td><span class="siuks-status ok">Aktif</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                                <tr><td>Pak Damar</td><td>WK-205</td><td>XI-2</td><td>IPS</td><td><span class="siuks-status ok">Aktif</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                                <tr><td>Bu Sari</td><td>WK-402</td><td>X-3</td><td>Bahasa</td><td><span class="siuks-status warn">Review</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="siuks-view-panel" data-panel="obat">
                <div class="siuks-detail-header">
                    <div>
                        <p class="siuks-page-kicker">Master</p>
                        <h2>Master Obat</h2>
                    </div>
                    <div class="siuks-detail-actions">
                        <button class="siuks-muted-btn" type="button">Rekap stok</button>
                        <button class="siuks-primary-btn" type="button">+ Tambah obat</button>
                    </div>
                </div>

                <div class="siuks-detail-cards">
                    <article class="siuks-mini-card"><span>Jenis obat</span><strong>35</strong><small>total SKU</small></article>
                    <article class="siuks-mini-card"><span>Stok cukup</span><strong>27</strong><small>77%</small></article>
                    <article class="siuks-mini-card"><span>Rendah</span><strong>6</strong><small>17%</small></article>
                    <article class="siuks-mini-card"><span>Kritis</span><strong>2</strong><small>6%</small></article>
                </div>

                <div class="siuks-record-panel">
                    <div class="siuks-record-toolbar">
                        <div class="siuks-text-search">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <input type="text" value="Cari obat atau kategori" aria-label="Cari obat">
                        </div>
                        <button class="siuks-mini-button" type="button">Tambah batch</button>
                    </div>
                    <div class="siuks-admin-table-wrap">
                        <table>
                            <thead><tr><th>Nama Obat</th><th>Kategori</th><th>Stok</th><th>Satuan</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <tr><td>Paracetamol</td><td>Analgesik</td><td>24</td><td>Box</td><td><span class="siuks-status ok">Cukup</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                                <tr><td>Amoxicillin</td><td>Antibiotik</td><td>9</td><td>Strip</td><td><span class="siuks-status warn">Rendah</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                                <tr><td>Betadine</td><td>Antiseptik</td><td>4</td><td>Botol</td><td><span class="siuks-status danger">Kritis</span></td><td><button class="siuks-mini-button" type="button">Edit</button></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="siuks-view-panel" data-panel="rekam-medis">
                <div class="siuks-page-header"><div><p class="siuks-page-kicker">Rekam</p><h2>Rekam Medis</h2></div><button class="siuks-primary-btn" type="button">Export PDF</button></div>
                <div class="siuks-admin-section-header"><div><p>Riwayat</p><h2>Data kunjungan</h2></div><div class="siuks-admin-tools"><input type="text" value="Cari nama / tanggal" aria-label="Cari rekam medis"><button type="button">Cari</button></div></div>
                <div class="siuks-admin-panel"><div class="siuks-admin-table-wrap"><table><thead><tr><th>Tanggal</th><th>Nama</th><th>Kelas</th><th>Keluhan</th><th>Obat</th><th>Status</th></tr></thead><tbody><tr><td>01 Okt 2026</td><td>Rina</td><td>X-1</td><td>Pusing</td><td>Paracetamol</td><td><span class="siuks-status ok">Pulang</span></td></tr><tr><td>03 Okt 2026</td><td>Ardi</td><td>XI-2</td><td>Demam</td><td>Amoxicillin</td><td><span class="siuks-status warn">Dirawat</span></td></tr><tr><td>05 Okt 2026</td><td>Fikri</td><td>X-2</td><td>Mual</td><td>Oralit</td><td><span class="siuks-status danger">Rujukan</span></td></tr></tbody></table></div></div>
            </section>

            <section class="siuks-view-panel" data-panel="monitoring-sos">
                <div class="siuks-page-header"><div><p class="siuks-page-kicker">Monitoring</p><h2>Monitoring SOS</h2></div><button class="siuks-primary-btn" type="button">+ Filter</button></div>
                <div class="siuks-admin-panel"><div class="siuks-admin-table-wrap"><table><thead><tr><th>Waktu</th><th>Nama</th><th>Kelas</th><th>Lokasi</th><th>Status</th></tr></thead><tbody><tr><td>09:20</td><td>Andi Pratama</td><td>X-1</td><td>Kelas X-1</td><td><span class="siuks-status ok">Ditangani</span></td></tr><tr><td>10:15</td><td>Siti Aisyah</td><td>XI-2</td><td>Lapangan</td><td><span class="siuks-status ok">Selesai</span></td></tr><tr><td>11:30</td><td>Rina Angustina</td><td>X-3</td><td>Kelas X-3</td><td><span class="siuks-status warn">Menunggu</span></td></tr></tbody></table></div></div>
            </section>

            <section class="siuks-view-panel" data-panel="laporan">
                <div class="siuks-page-header"><div><p class="siuks-page-kicker">Laporan</p><h2>Laporan & Export</h2></div><button class="siuks-primary-btn" type="button">Ekspor PDF</button></div>
                <div class="siuks-admin-panel"><div class="siuks-admin-table-wrap"><table><thead><tr><th>Jenis</th><th>Periode</th><th>Jumlah</th><th>Status</th><th>Detail</th></tr></thead><tbody><tr><td>Kunjungan UKS</td><td>Sep 2026</td><td>87</td><td><span class="siuks-status ok">Siap</span></td><td>Download</td></tr><tr><td>Stok Obat</td><td>Sep 2026</td><td>35</td><td><span class="siuks-status warn">Review</span></td><td>Download</td></tr><tr><td>SOS</td><td>Sep 2026</td><td>6</td><td><span class="siuks-status ok">Siap</span></td><td>Download</td></tr></tbody></table></div></div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
