<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eaf3fc">
    <title>Master Obat | SiUKS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="siuks-body">
<div class="siuks-app siuks-student-page siuks-medicine-admin-page" data-validation-errors="{{ $errors->any() ? 'true' : 'false' }}">
    <aside class="siuks-sidebar">
        <a class="siuks-brand" href="{{ route('dashboard') }}" aria-label="SiUKS, dashboard"><span class="siuks-brand-icon" aria-hidden="true"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""></span><span class="siuks-brand-text"><strong>SiUKS</strong><small>Sistem Informasi Kesehatan Sekolah</small></span></a>
        <nav class="siuks-navigation" aria-label="Navigasi utama">
            <a class="siuks-nav-item" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z"/></svg><span>Dashboard</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.siswa.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20v-1.3A5.7 5.7 0 0 1 8.2 13h1.6a5.7 5.7 0 0 1 5.7 5.7V20H2.5ZM17 5a3.2 3.2 0 0 1 0 6.2M17 14a4.7 4.7 0 0 1 4.5 4.7V20h-4"/></svg><span>Data Siswa</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.wali-kelas.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20v-1a6.5 6.5 0 0 1 13 0v1h-13ZM15 14a5 5 0 0 1 6.5 4.8V20H17"/></svg><span>Data Wali Kelas</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.pmr.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7.5" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2H4Z"/></svg><span>Data Petugas PMR</span></a>
            <a class="siuks-nav-item is-active" href="{{ route('admin.obat.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.1 3.5a5.5 5.5 0 0 1 7.8 0l4.6 4.6a5.5 5.5 0 0 1-7.8 7.8l-4.6-4.6a5.5 5.5 0 0 1 0-7.8Z"/><path d="m9.2 12 5.6-5.6" stroke="#fff" stroke-width="1.6"/></svg><span>Master Obat</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.rekam-medis.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Rekam Medis</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.sos.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 22 20H2L12 3Z"/><path d="M12 9v5m0 3h.01" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg><span>Monitoring SOS</span></a>
            <a class="siuks-nav-item" href="{{ route('admin.laporan.index') }}"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h10l4 4v14H5V3Z"/><path d="M14 3v5h5M8 12h8m-8 4h8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg><span>Laporan</span></a>
        </nav>
        <div class="siuks-sidebar-foot"><span class="siuks-sidebar-status"></span>Sistem UKS Sekolah<small>v1.0</small></div>
    </aside>
    <main class="siuks-main">
        <header class="siuks-topbar">
            <button class="siuks-sidebar-toggle" type="button" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi">⋯</button>
            <div class="siuks-top-context"><span>Panel Admin</span><strong>Master Obat</strong></div>
            <div class="siuks-top-actions"><div class="siuks-admin"><span class="siuks-admin-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="siuks-logout" type="submit">Logout</button></form></div>
        </header>
        <div class="siuks-content">
            <section class="siuks-heading"><div><h1>Master Obat</h1><p>Kelola persediaan dan pantau ketersediaan obat UKS</p></div><button class="siuks-student-add" type="button" data-open-admin-medicine-create>＋ Tambah obat</button></section>
            @if (session('status'))<div class="siuks-student-notice notice-success" role="status">{{ session('status') }}</div>@endif
            @if (session('error'))<div class="siuks-student-notice notice-error" role="alert">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="siuks-student-notice notice-error" role="alert">Periksa kembali data obat yang dimasukkan.</div>@endif
            <section class="siuks-student-stats" aria-label="Ringkasan stok obat">
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-blue" aria-hidden="true">▤</span><div><span>Total jenis obat</span><strong>{{ $totalMedicines }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-red" aria-hidden="true">!</span><div><span>Stok kritis (≤ {{ $criticalStockThreshold }})</span><strong>{{ $criticalMedicines }}</strong></div></article>
                <article class="siuks-stat"><span class="siuks-stat-icon siuks-slate" aria-hidden="true">✓</span><div><span>Stok aman</span><strong>{{ $totalMedicines - $criticalMedicines }}</strong></div></article>
            </section>
            <section class="siuks-panel siuks-student-panel">
                <div class="siuks-student-panel-heading"><div><h2>Daftar master obat</h2><p>Stok {{ $criticalStockThreshold }} atau kurang ditandai kritis.</p></div></div>
                <form class="siuks-student-filters siuks-medicine-filters" method="GET" action="{{ route('admin.obat.index') }}">
                    <label><span>Cari nama atau kategori</span><input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama, kode, kategori..."></label>
                    <label><span>Kategori</span><select name="category"><option value="">Semua kategori</option>@foreach ($categories as $category)<option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>@endforeach</select></label>
                    <label><span>Status stok</span><select name="status"><option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Semua status</option><option value="aman" @selected(($filters['status'] ?? '') === 'aman')>Aman</option><option value="kritis" @selected(($filters['status'] ?? '') === 'kritis')>Kritis</option></select></label>
                    <button type="submit" class="siuks-student-filter-submit">Cari obat</button>
                </form>
                <div class="siuks-table-scroll siuks-student-table-wrap">
                    <table class="siuks-student-table siuks-medicine-table"><thead><tr><th>Nama obat</th><th>Kategori</th><th>Stok</th><th>Satuan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                        @forelse ($medicines as $medicine)
                            @php($isCritical = $medicine->stok_tersedia <= $criticalStockThreshold)
                            <tr>
                                <td><strong>{{ $medicine->nama_obat }}</strong><small>Kode {{ $medicine->kode_obat }} · {{ $medicine->jenis }}</small></td>
                                <td>{{ $medicine->kategori }}</td>
                                <td><strong>{{ number_format($medicine->stok_tersedia) }}</strong></td>
                                <td>{{ $medicine->satuan }}</td>
                                <td><span class="siuks-pill {{ $isCritical ? 'pill-sick' : 'pill-healthy' }}">{{ $isCritical ? 'Kritis' : 'Aman' }}</span></td>
                                <td><div class="siuks-student-actions">
                                    <button type="button" class="siuks-student-edit" data-edit-admin-medicine data-id="{{ $medicine->id }}" data-url="{{ route('admin.obat.update', $medicine->id) }}" data-code="{{ $medicine->kode_obat }}" data-name="{{ $medicine->nama_obat }}" data-kind="{{ $medicine->jenis }}" data-category="{{ $medicine->kategori }}" data-stock="{{ $medicine->stok_tersedia }}" data-unit="{{ $medicine->satuan }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.obat.destroy', $medicine->id) }}" data-delete-admin-medicine>@csrf @method('DELETE')<button type="submit" class="siuks-student-delete">Hapus</button></form>
                                </div></td>
                            </tr>
                        @empty
                            <tr><td class="siuks-student-empty" colspan="6">Belum ada data obat yang cocok dengan pencarian ini.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
                @if ($medicines->hasPages())<div class="siuks-student-pagination">{{ $medicines->links() }}</div>@endif
            </section>
            <footer class="siuks-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Status kritis menggunakan batas stok ≤ {{ $criticalStockThreshold }}</span></footer>
        </div>
    </main>
    <dialog class="siuks-student-dialog" id="admin-medicine-dialog" data-admin-medicine-dialog data-kind="medicine">
        <form method="POST" action="{{ route('admin.obat.store') }}" class="siuks-student-form" data-admin-medicine-form data-store-url="{{ route('admin.obat.store') }}" data-invalid="{{ $errors->any() ? 'true' : 'false' }}" data-old-code="{{ old('kode_obat') }}" data-old-name="{{ old('nama_obat') }}" data-old-kind="{{ old('jenis') }}" data-old-category="{{ old('kategori') }}" data-old-stock="{{ old('stok_tersedia') }}" data-old-unit="{{ old('satuan') }}">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="siuks-student-dialog-heading"><div><p>Persediaan UKS</p><h2 data-admin-medicine-title>Tambah obat</h2></div><button type="button" data-close-admin-medicine-dialog aria-label="Tutup">×</button></div>
            @foreach (['kode_obat' => 'Kode obat', 'nama_obat' => 'Nama obat', 'jenis' => 'Jenis', 'kategori' => 'Kategori', 'stok_tersedia' => 'Stok tersedia', 'satuan' => 'Satuan (tablet/botol)'] as $field => $label)
                <label><span>{{ $label }}</span><input name="{{ $field }}" type="{{ $field === 'stok_tersedia' ? 'number' : 'text' }}" value="{{ old($field) }}" required min="{{ $field === 'stok_tersedia' ? 0 : '' }}" step="{{ $field === 'stok_tersedia' ? 1 : '' }}" maxlength="255">@error($field)<small>{{ $message }}</small>@enderror</label>
            @endforeach
            <p class="siuks-student-form-note">Stok lima atau kurang akan ditandai kritis, baik pada halaman Admin maupun PMR.</p>
            <div class="siuks-student-form-actions"><button type="button" data-close-admin-medicine-dialog>Batal</button><button type="submit" data-admin-medicine-submit>Simpan obat</button></div>
        </form>
    </dialog>
</div>
</body>
</html>
