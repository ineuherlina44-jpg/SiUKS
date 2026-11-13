<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eaf3fc">
    <title>Rekam Medis | SiUKS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body">
<div class="siuks-app siuks-student-page siuks-medical-admin-page">
    <aside class="siuks-sidebar">
        <a class="siuks-brand" href="{{ route('dashboard') }}" aria-label="SiUKS, dashboard"><span class="siuks-brand-icon" aria-hidden="true"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""></span><span class="siuks-brand-text"><strong>SiUKS</strong><small>Sistem Informasi Kesehatan Sekolah</small></span></a>
        <nav class="siuks-navigation" aria-label="Navigasi utama">
            <a class="siuks-nav-item" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z"/></svg><span>Dashboard</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.siswa.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20v-1.3A5.7 5.7 0 0 1 8.2 13h1.6a5.7 5.7 0 0 1 5.7 5.7V20H2.5ZM17 5a3.2 3.2 0 0 1 0 6.2M17 14a4.7 4.7 0 0 1 4.5 4.7V20h-4"/></svg><span>Data Siswa</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.wali-kelas.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20v-1a6.5 6.5 0 0 1 13 0v1h-13ZM15 14a5 5 0 0 1 6.5 4.8V20H17"/></svg><span>Data Wali Kelas</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.pmr.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7.5" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg><span>Data Petugas PMR</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.obat.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.1 3.5a5.5 5.5 0 0 1 7.8 0l4.6 4.6a5.5 5.5 0 0 1-7.8 7.8l-4.6-4.6a5.5 5.5 0 0 1 0-7.8Z"/><path d="m9.2 12 5.6-5.6" stroke="#fff" stroke-width="1.6"/></svg><span>Master Obat</span></a>
            <a class="siuks-nav-item is-active" href="{{ route('admin.rekam-medis.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Rekam Medis</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.sos.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 22 20H2L12 3Z"/><path d="M12 9v5m0 3h.01" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg><span>Monitoring SOS</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.laporan.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Laporan</span></a>
        </nav>
        <div class="siuks-sidebar-foot"><span class="siuks-sidebar-status"></span>Sistem UKS Sekolah<small>v1.0</small></div>
    </aside>
    <main class="siuks-main">
        <header class="siuks-topbar">
            <button class="siuks-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi">⋯</button>
            <div class="siuks-top-context"><span>Panel Admin</span><strong>Rekam Medis</strong></div>
            <div class="siuks-top-actions"><div class="siuks-admin"><span class="siuks-admin-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="siuks-logout" type="submit">Logout</button></form></div>
        </header>
        <div class="siuks-content">
            <section class="siuks-heading siuks-medical-heading">
                <div><h1>Riwayat Rekam Medis</h1><p>Telusuri catatan pemeriksaan dan pelayanan kesehatan siswa</p></div>
                <a class="siuks-student-add" href="{{ route('admin.rekam-medis.download', $filters) }}">↓ Ekspor PDF</a>
            </section>
            @if ($errors->any())<div class="siuks-student-notice notice-error" role="alert">Periksa kembali rentang tanggal dan filter yang dipilih.</div>@endif
            <section class="siuks-panel siuks-student-panel siuks-medical-panel">
                <form class="siuks-student-filters siuks-medical-filters" method="GET" action="{{ route('admin.rekam-medis.index') }}">
                    <label><span>Cari nama, NISN, keluhan, diagnosis, atau tindakan</span><input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama siswa, NISN, diagnosis..."></label>
                    <label><span>Jenis pemeriksaan</span><select name="type"><option value="">Semua jenis</option>@foreach ($types as $type)<option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ $type }}</option>@endforeach</select></label>
                    <label><span>Status kunjungan</span><select name="status"><option value="">Semua status</option><option value="dirawat" @selected(($filters['status'] ?? '') === 'dirawat')>Sedang dirawat</option><option value="diizinkan_pulang" @selected(($filters['status'] ?? '') === 'diizinkan_pulang')>Diizinkan pulang</option><option value="rujukan" @selected(($filters['status'] ?? '') === 'rujukan')>Rujukan</option></select></label>
                    <label><span>Dari tanggal</span><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></label>
                    <label><span>Sampai tanggal</span><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></label>
                    <button class="siuks-student-filter-submit" type="submit">Terapkan filter</button>
                </form>
                <div class="siuks-medical-table-heading">
                    <div><h2>Catatan pemeriksaan</h2><p>{{ number_format($records->total()) }} catatan ditemukan</p></div>
                    <a href="{{ route('admin.rekam-medis.index') }}">Reset filter</a>
                </div>
                <div class="siuks-table-scroll siuks-student-table-wrap">
                    <table class="siuks-student-table siuks-medical-table"><thead><tr><th>Tanggal</th><th>Nama siswa</th><th>Jenis pemeriksaan</th><th>Kelas</th><th>Keluhan</th><th>Obat</th><th>Status</th></tr></thead><tbody>
                        @forelse ($records as $record)
                            @php
                                $statusLabel = match ($record->status) {
                                    'diizinkan_pulang' => 'Diizinkan pulang',
                                    'dirawat' => 'Sedang dirawat',
                                    'rujukan' => 'Rujukan',
                                    default => ucfirst($record->status),
                                };
                                $statusClass = match ($record->status) {
                                    'diizinkan_pulang' => 'pill-healthy',
                                    'dirawat' => 'pill-pending',
                                    'rujukan' => 'pill-referral',
                                    default => '',
                                };
                            @endphp
                            <tr>
                                <td><strong>{{ \Illuminate\Support\Carbon::parse($record->waktu_pemeriksaan)->format('d M Y') }}</strong><small>{{ \Illuminate\Support\Carbon::parse($record->waktu_pemeriksaan)->format('H:i') }} WIB</small></td>
                                <td><strong>{{ $record->nama_siswa }}</strong><small>NISN {{ $record->nisn }}</small></td>
                                <td>{{ $record->status_penyakit ?: 'Tidak dicatat' }}</td>
                                <td>{{ $record->kelas }}</td>
                                <td class="siuks-medical-complaint" title="{{ $record->keluhan }}">{{ \Illuminate\Support\Str::limit($record->keluhan ?: 'Tidak dicatat', 90) }}</td>
                                <td>
                                    @forelse ($record->medicines as $medicine)
                                        <strong class="siuks-medical-medicine">{{ $medicine->nama_obat }}</strong><small>{{ $medicine->jumlah_dosis }} dosis · {{ $medicine->aturan_pakai }}</small>
                                    @empty
                                        <span class="siuks-medical-muted">Tidak ada obat</span>
                                    @endforelse
                                </td>
                                <td><span class="siuks-pill {{ $statusClass }}">{{ $statusLabel }}</span></td>
                            </tr>
                        @empty
                            <tr><td class="siuks-student-empty" colspan="7">Tidak ada catatan rekam medis yang sesuai dengan filter.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
                @if ($records->hasPages())<div class="siuks-student-pagination">{{ $records->links() }}</div>@endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
