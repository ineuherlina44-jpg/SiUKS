<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#EEF4FB">
    <title>Kelola Stok Obat | SiUKS</title>
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
                <div class="pmr-topbar-title"><span>Portal Petugas PMR</span><strong>Kelola stok obat</strong></div>
                <div class="pmr-account">
                    <span class="pmr-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <span class="pmr-account-name">{{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="pmr-logout" type="submit">Logout</button></form>
                </div>
            </header>

            <div class="pmr-content pmr-medicine-content">
                <section class="pmr-page-heading">
                    <div><p>Persediaan UKS</p><h1>Kelola Stok Obat</h1><span>Tambah, perbarui, dan pantau ketersediaan obat UKS.</span></div>
                    <button class="pmr-medicine-add" type="button" data-open-medicine-dialog><span aria-hidden="true">＋</span>Tambah obat</button>
                </section>

                @if (session('status'))
                    <p class="pmr-form-success" role="status">{{ session('status') }}</p>
                @endif
                @if ($errors->any())
                    <div class="pmr-form-errors" role="alert">
                        <strong>Periksa kembali data obat.</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <section class="pmr-medicine-summary" aria-label="Ringkasan stok obat">
                    <article><span class="pmr-medicine-summary-icon">▤</span><div><span>Total jenis obat</span><strong>{{ $totalMedicines }}</strong></div></article>
                    <article><span class="pmr-medicine-summary-icon is-critical">!</span><div><span>Stok kritis (≤ {{ $criticalStockThreshold }})</span><strong>{{ $criticalMedicines }}</strong></div></article>
                </section>

                <section class="pmr-panel pmr-medicine-panel" aria-labelledby="pmr-medicine-table-title">
                    <div class="pmr-medicine-toolbar">
                        <div><h2 id="pmr-medicine-table-title">Daftar obat</h2><p>Stok {{ $criticalStockThreshold }} atau kurang ditandai kritis.</p></div>
                        <form class="pmr-medicine-search" method="GET" action="{{ route('pmr.obat.index') }}">
                            <label for="medicine-search">Cari obat</label>
                            <div><input id="medicine-search" type="search" name="search" value="{{ $search }}" placeholder="Nama, kode, kategori..."><button type="submit" aria-label="Cari">⌕</button></div>
                            @if ($search !== '')<a href="{{ route('pmr.obat.index') }}">Hapus pencarian</a>@endif
                        </form>
                    </div>

                    <div class="pmr-table-wrap">
                        <table class="pmr-medicine-table">
                            <thead><tr><th scope="col">Obat</th><th scope="col">Kategori / jenis</th><th scope="col">Stok tersedia</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead>
                            <tbody>
                                @forelse ($medicines as $medicine)
                                    @php($isCritical = $medicine->stok_tersedia <= $criticalStockThreshold)
                                    <tr>
                                        <td><strong>{{ $medicine->nama_obat }}</strong><small>Kode {{ $medicine->kode_obat }}</small></td>
                                        <td><strong>{{ $medicine->kategori }}</strong><small>{{ $medicine->jenis }}</small></td>
                                        <td><strong>{{ $medicine->stok_tersedia }}</strong> {{ $medicine->satuan }}</td>
                                        <td><span class="pmr-medicine-status {{ $isCritical ? 'is-critical' : 'is-safe' }}"><i aria-hidden="true"></i>{{ $isCritical ? 'Kritis' : 'Aman' }}</span></td>
                                        <td>
                                            <div class="pmr-medicine-actions">
                                                <button class="pmr-medicine-edit" type="button" data-edit-medicine
                                                    data-id="{{ $medicine->id }}"
                                                    data-code="{{ $medicine->kode_obat }}"
                                                    data-name="{{ $medicine->nama_obat }}"
                                                    data-kind="{{ $medicine->jenis }}"
                                                    data-category="{{ $medicine->kategori }}"
                                                    data-stock="{{ $medicine->stok_tersedia }}"
                                                    data-unit="{{ $medicine->satuan }}">Edit</button>
                                                <form method="POST" action="{{ route('pmr.obat.destroy', $medicine->id) }}" data-delete-medicine>
                                                    @csrf @method('DELETE')
                                                    <button class="pmr-medicine-delete" type="submit">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="pmr-table-empty">{{ $search !== '' ? 'Obat tidak ditemukan untuk pencarian ini.' : 'Belum ada data obat. Tambahkan obat pertama untuk mulai mengelola stok.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($medicines->hasPages())
                        <div class="pmr-medicine-pagination">{{ $medicines->links() }}</div>
                    @endif
                </section>

                <div class="pmr-medicine-refresh">
                    <div><strong>Data stok diperbarui langsung</strong><span>Gunakan tombol refresh untuk memuat ulang persediaan terbaru.</span></div>
                    <a href="{{ route('pmr.obat.index', array_filter(['search' => $search])) }}" aria-label="Refresh stok obat">⟳ Refresh stok obat</a>
                </div>
                <footer class="pmr-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Informasi stok mengikuti data persediaan UKS</span></footer>
            </div>
        </main>
    </div>

    <dialog class="pmr-medicine-dialog" id="medicine-dialog" aria-labelledby="medicine-dialog-title">
        <form method="POST" id="medicine-form" action="{{ old('medicine_id') ? route('pmr.obat.update', old('medicine_id')) : route('pmr.obat.store') }}" data-invalid="{{ $errors->any() ? 'true' : 'false' }}">
            @csrf
            <input type="hidden" name="_method" id="medicine-method" value="{{ old('_method', 'POST') }}">
            <input type="hidden" name="medicine_id" id="medicine-id" value="{{ old('medicine_id') }}">
            <div class="pmr-medicine-dialog-heading"><div><p>Data persediaan UKS</p><h2 id="medicine-dialog-title">Tambah obat</h2></div><button type="button" data-close-medicine-dialog aria-label="Tutup">×</button></div>
            <div class="pmr-medicine-dialog-grid">
                <div class="pmr-field"><label for="medicine-code">Kode obat <span>*</span></label><input id="medicine-code" name="kode_obat" value="{{ old('kode_obat') }}" required maxlength="255"></div>
                <div class="pmr-field"><label for="medicine-name">Nama obat <span>*</span></label><input id="medicine-name" name="nama_obat" value="{{ old('nama_obat') }}" required maxlength="255"></div>
                <div class="pmr-field"><label for="medicine-kind">Jenis <span>*</span></label><input id="medicine-kind" name="jenis" value="{{ old('jenis') }}" required maxlength="255" placeholder="Contoh: Tablet"></div>
                <div class="pmr-field"><label for="medicine-category">Kategori <span>*</span></label><input id="medicine-category" name="kategori" value="{{ old('kategori') }}" required maxlength="255" placeholder="Contoh: Analgesik"></div>
                <div class="pmr-field"><label for="medicine-stock">Stok tersedia <span>*</span></label><input id="medicine-stock" name="stok_tersedia" value="{{ old('stok_tersedia') }}" type="number" min="0" step="1" required></div>
                <div class="pmr-field"><label for="medicine-unit">Satuan <span>*</span></label><input id="medicine-unit" name="satuan" value="{{ old('satuan') }}" required maxlength="255" placeholder="Contoh: tablet"></div>
            </div>
            <div class="pmr-medicine-dialog-actions"><button type="button" class="pmr-reset-button" data-close-medicine-dialog>Batal</button><button type="submit" class="pmr-submit-button">Simpan obat</button></div>
        </form>
    </dialog>
</body>
</html>
