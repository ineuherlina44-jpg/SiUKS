<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eaf3fc">
    <title>Monitoring SOS | SiUKS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body">
<div class="siuks-app siuks-student-page siuks-sos-admin-page">
    <aside class="siuks-sidebar">
        <a class="siuks-brand" href="{{ route('dashboard') }}" aria-label="SiUKS, dashboard"><span class="siuks-brand-icon" aria-hidden="true"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""></span><span class="siuks-brand-text"><strong>SiUKS</strong><small>Sistem Informasi Kesehatan Sekolah</small></span></a>
        <nav class="siuks-navigation" aria-label="Navigasi utama">
            <a class="siuks-nav-item" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z"/></svg><span>Dashboard</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.siswa.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20v-1.3A5.7 5.7 0 0 1 8.2 13h1.6a5.7 5.7 0 0 1 5.7 5.7V20H2.5ZM17 5a3.2 3.2 0 0 1 0 6.2M17 14a4.7 4.7 0 0 1 4.5 4.7V20h-4"/></svg><span>Data Siswa</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.wali-kelas.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20v-1a6.5 6.5 0 0 1 13 0v1h-13ZM15 14a5 5 0 0 1 6.5 4.8V20H17"/></svg><span>Data Wali Kelas</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.pmr.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7.5" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg><span>Data Petugas PMR</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.obat.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.1 3.5a5.5 5.5 0 0 1 7.8 0l4.6 4.6a5.5 5.5 0 0 1-7.8 7.8l-4.6-4.6a5.5 5.5 0 0 1 0-7.8Z"/><path d="m9.2 12 5.6-5.6" stroke="#fff" stroke-width="1.6"/></svg><span>Master Obat</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.rekam-medis.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Rekam Medis</span></a>
            <a class="siuks-nav-item is-active" href="{{ route('admin.sos.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 22 20H2L12 3Z"/><path d="M12 9v5m0 3h.01" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg><span>Monitoring SOS</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.laporan.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Laporan</span></a>
        </nav>
        <div class="siuks-sidebar-foot"><span class="siuks-sidebar-status"></span>Sistem UKS Sekolah<small>v1.0</small></div>
    </aside>
    <main class="siuks-main">
        <header class="siuks-topbar">
            <button class="siuks-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi">⋯</button>
            <div class="siuks-top-context"><span>Panel Admin</span><strong>Monitoring SOS</strong></div>
            <div class="siuks-top-actions"><div class="siuks-admin"><span class="siuks-admin-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="siuks-logout" type="submit">Logout</button></form></div>
        </header>
        <div class="siuks-content">
            <section class="siuks-heading siuks-sos-heading">
                <div><h1>Monitoring Sinyal SOS</h1><p>Pantau laporan darurat dan perbarui status penanganannya</p></div>
                <form class="siuks-report-period" method="GET" action="{{ route('admin.sos.index') }}">
                    <label><span>Dari tanggal</span><input type="date" name="date_from" value="{{ $dateFrom }}" required></label>
                    <label><span>Sampai tanggal</span><input type="date" name="date_to" value="{{ $dateTo }}" required></label>
                    <label><span>Status</span><select name="status"><option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Semua status</option><option value="menunggu" @selected(($filters['status'] ?? '') === 'menunggu')>Menunggu</option><option value="ditangani" @selected(($filters['status'] ?? '') === 'ditangani')>Ditangani</option><option value="selesai" @selected(($filters['status'] ?? '') === 'selesai')>Selesai</option></select></label>
                    <button class="siuks-student-filter-submit" type="submit">Terapkan</button>
                </form>
            </section>
            @if (session('status'))<div class="siuks-student-notice notice-success" role="status">{{ session('status') }}</div>@endif
            @if ($errors->any())<div class="siuks-student-notice notice-error" role="alert">Status atau rentang tanggal tidak valid. Periksa kembali pilihan Anda.</div>@endif
            <section class="siuks-student-stats siuks-sos-stats" aria-label="Ringkasan sinyal SOS pada periode terpilih">
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-blue" aria-hidden="true">!</span><div><span>Total sinyal SOS</span><strong>{{ number_format($totalAlerts) }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-red" aria-hidden="true">◷</span><div><span>Menunggu ditangani</span><strong>{{ number_format($waitingAlerts) }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate" aria-hidden="true">✓</span><div><span>Sudah ditangani / selesai</span><strong>{{ number_format($handledAlerts) }}</strong></div></article>
            </section>
            <section class="siuks-panel siuks-student-panel siuks-sos-panel">
                <div class="siuks-student-panel-heading"><div><h2>Daftar sinyal SOS</h2><p>Lokasi belum tersedia karena belum dicatat pada data sinyal SOS.</p></div><span class="siuks-sos-period">{{ $dateFrom }} – {{ $dateTo }}</span></div>
                <div class="siuks-table-scroll siuks-student-table-wrap">
                    <table class="siuks-student-table siuks-sos-table"><thead><tr><th>Waktu</th><th>Nama siswa</th><th>Kelas</th><th>Lokasi</th><th>Status</th><th>Aksi status</th></tr></thead><tbody>
                        @forelse ($alerts as $alert)
                            @php($status = mb_strtolower($alert->status_respon))
                            <tr>
                                <td><strong>{{ \Illuminate\Support\Carbon::parse($alert->waktu_pemicu)->format('d M Y') }}</strong><small>{{ \Illuminate\Support\Carbon::parse($alert->waktu_pemicu)->format('H:i') }} WIB</small></td>
                                <td><strong>{{ $alert->nama_siswa }}</strong></td>
                                <td>{{ $alert->kelas }}</td>
                                <td><span class="siuks-sos-location">Belum dicatat</span></td>
                                <td><span class="siuks-pill {{ $status === 'selesai' ? 'pill-healthy' : ($status === 'menunggu' ? 'pill-pending' : 'pill-handled') }}">{{ ucfirst($status) }}</span></td>
                                <td>
                                    <form class="siuks-sos-status-form" method="POST" action="{{ route('admin.sos.status', $alert->id) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                                        <input type="hidden" name="date_to" value="{{ $dateTo }}">
                                        <input type="hidden" name="status" value="{{ $filters['status'] ?? 'all' }}">
                                        <label class="siuks-sr-only" for="sos-status-{{ $alert->id }}">Ubah status sinyal {{ $alert->nama_siswa }}</label>
                                        <select id="sos-status-{{ $alert->id }}" name="status_respon">
                                            <option value="menunggu" @selected($status === 'menunggu')>Menunggu</option>
                                            <option value="ditangani" @selected($status === 'ditangani')>Ditangani</option>
                                            <option value="selesai" @selected($status === 'selesai')>Selesai</option>
                                        </select>
                                        <button type="submit">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="siuks-student-empty" colspan="6">Tidak ada sinyal SOS pada tanggal dan status yang dipilih.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
                @if ($alerts->hasPages())<div class="siuks-student-pagination">{{ $alerts->links() }}</div>@endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
