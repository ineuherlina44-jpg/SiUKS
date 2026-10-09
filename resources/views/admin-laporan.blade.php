<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eaf3fc">
    <title>Laporan | SiUKS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body">
<div class="siuks-app siuks-student-page siuks-report-page">
    <aside class="siuks-sidebar">
        <a class="siuks-brand" href="{{ route('dashboard') }}" aria-label="SiUKS, dashboard"><span class="siuks-brand-icon" aria-hidden="true"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""></span><span class="siuks-brand-text"><strong>SiUKS</strong><small>Sistem Informasi Kesehatan Sekolah</small></span></a>
        <nav class="siuks-navigation" aria-label="Navigasi utama">
            <a class="siuks-nav-item" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z"/></svg><span>Dashboard</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.siswa.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20v-1.3A5.7 5.7 0 0 1 8.2 13h1.6a5.7 5.7 0 0 1 5.7 5.7V20H2.5ZM17 5a3.2 3.2 0 0 1 0 6.2M17 14a4.7 4.7 0 0 1 4.5 4.7V20h-4"/></svg><span>Data Siswa</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.wali-kelas.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20v-1a6.5 6.5 0 0 1 13 0v1h-13ZM15 14a5 5 0 0 1 6.5 4.8V20H17"/></svg><span>Data Wali Kelas</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.pmr.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7.5" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg><span>Data Petugas PMR</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.obat.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.1 3.5a5.5 5.5 0 0 1 7.8 0l4.6 4.6a5.5 5.5 0 0 1-7.8 7.8l-4.6-4.6a5.5 5.5 0 0 1 0-7.8Z"/><path d="m9.2 12 5.6-5.6" stroke="#fff" stroke-width="1.6"/></svg><span>Master Obat</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.rekam-medis.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Rekam Medis</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.sos.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 22 20H2L12 3Z"/><path d="M12 9v5m0 3h.01" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg><span>Monitoring SOS</span></a>
            <a class="siuks-nav-item is-active" href="{{ route('admin.laporan.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Laporan</span></a>
        </nav>
        <div class="siuks-sidebar-foot"><span class="siuks-sidebar-status"></span>Sistem UKS Sekolah<small>v1.0</small></div>
    </aside>
    <main class="siuks-main">
        <header class="siuks-topbar">
            <button class="siuks-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi">⋯</button>
            <div class="siuks-top-context"><span>Panel Admin</span><strong>Laporan UKS</strong></div>
            <div class="siuks-top-actions"><div class="siuks-admin"><span class="siuks-admin-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="siuks-logout" type="submit">Logout</button></form></div>
        </header>
        <div class="siuks-content">
            <section class="siuks-heading siuks-report-heading">
                <div><h1>Laporan Kunjungan UKS</h1><p>Ringkasan dan detail layanan kesehatan siswa</p></div>
                <form class="siuks-report-period" method="GET" action="{{ route('admin.laporan.index') }}">
                    <label><span>Dari tanggal</span><input type="date" name="date_from" value="{{ $dateFrom }}" required></label>
                    <label><span>Sampai tanggal</span><input type="date" name="date_to" value="{{ $dateTo }}" required></label>
                    <button class="siuks-student-filter-submit" type="submit">Terapkan</button>
                    <a class="siuks-student-add" href="{{ route('admin.laporan.download', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}">↓ Unduh PDF</a>
                </form>
            </section>
            @if ($errors->any())
                <div class="siuks-student-notice notice-error" role="alert">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
            @endif
            <section class="siuks-student-stats siuks-report-stats" aria-label="Ringkasan kunjungan pada periode terpilih">
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-blue" aria-hidden="true">▤</span><div><span>Total kunjungan</span><strong>{{ number_format($totalVisits) }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate" aria-hidden="true">♙</span><div><span>Siswa unik berkunjung</span><strong>{{ number_format($uniqueStudents) }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-red" aria-hidden="true">◷</span><div><span>Rata-rata kunjungan / hari</span><strong>{{ number_format($dailyAverage, 1, ',', '.') }}</strong></div></article>
            </section>
            <section class="siuks-charts-grid siuks-report-charts">
                <article class="siuks-panel">
                    <div class="siuks-report-panel-heading"><div><h2>Kunjungan UKS per bulan</h2><p>Jumlah kunjungan dan siswa unik · {{ \Illuminate\Support\Carbon::parse($dateFrom)->year }}</p></div><div class="siuks-report-legend"><span><i></i>Kunjungan</span><span><i></i>Siswa unik</span></div></div>
                    <div class="siuks-month-chart" role="img" aria-label="Grafik jumlah kunjungan UKS dan siswa unik per bulan">
                        @foreach ($monthlyChart as $month)
                            <div class="siuks-month-column" aria-label="{{ $month['label'] }}: {{ $month['visits'] }} kunjungan, {{ $month['students'] }} siswa unik">
                                <div class="siuks-month-bars">
                                    <span class="bar-visits" style="height: {{ $month['visits'] ? max(3, ($month['visits'] / $maxMonthlyValue) * 100) : 0 }}%"></span>
                                    <span class="bar-students" style="height: {{ $month['students'] ? max(3, ($month['students'] / $maxMonthlyValue) * 100) : 0 }}%"></span>
                                </div>
                                <small>{{ $month['label'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </article>
                <article class="siuks-panel">
                    <div class="siuks-report-panel-heading"><div><h2>Keluhan terbanyak</h2><p>Berdasarkan diagnosis atau keluhan pada periode.</p></div></div>
                    <div class="siuks-report-donut-wrap">
                        <div class="siuks-report-donut" role="img" aria-label="Diagram keluhan terbanyak" style="background: conic-gradient({{ $complaintGradient }})">
                            <div><span>Total</span><strong>{{ number_format($totalVisits) }}</strong></div>
                        </div>
                        <ul class="siuks-report-legend">
                            @forelse ($complaintSlices as $slice)
                                <li><i style="background: {{ $slice['color'] }}"></i><span>{{ $slice['name'] }}</span><b>{{ $slice['percentage'] }}%</b></li>
                            @empty
                                <li class="siuks-report-no-data">Belum ada data keluhan.</li>
                            @endforelse
                        </ul>
                    </div>
                </article>
            </section>
            <section class="siuks-panel siuks-report-details">
                <div class="siuks-report-panel-heading"><div><h2>Detail data kunjungan</h2><p>{{ $visits->total() }} data pada periode {{ \Illuminate\Support\Carbon::parse($dateFrom)->format('d/m/Y') }} – {{ \Illuminate\Support\Carbon::parse($dateTo)->format('d/m/Y') }}</p></div></div>
                <div class="siuks-table-scroll siuks-report-table-wrap">
                    <table class="siuks-report-table"><thead><tr><th>Tanggal & waktu</th><th>Nama siswa</th><th>Kelas</th><th>Keluhan / diagnosis</th></tr></thead><tbody>
                        @forelse ($visits as $visit)
                            <tr><td>{{ \Illuminate\Support\Carbon::parse($visit->waktu_pemeriksaan)->format('d/m/Y H:i') }}</td><td><strong>{{ $visit->nama_siswa }}</strong></td><td>{{ $visit->kelas }}</td><td>{{ $visit->status_penyakit ?: ($visit->keluhan ?: 'Tidak dicatat') }}</td></tr>
                        @empty
                            <tr><td class="siuks-student-empty" colspan="4">Belum ada kunjungan pada periode ini.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
                @if ($visits->hasPages())<div class="siuks-student-pagination">{{ $visits->links() }}</div>@endif
            </section>
            <footer class="siuks-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Laporan berdasarkan data rekam medis tersimpan</span></footer>
        </div>
    </main>
</div>
</body>
</html>
