<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#EAF3FC">
    <title>Cek Stok Obat | SiUKS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="student-stock-page">
    <div class="student-stock-app">
        <aside class="student-sidebar">
            <a class="student-brand" href="{{ route('dashboard') }}" aria-label="SiUKS dashboard siswa">
                <img src="{{ asset('images/siuks-logo.png.png') }}" alt="">
                <span><strong>SiUKS</strong><small>Portal Siswa</small></span>
            </a>
            <nav class="student-navigation" aria-label="Navigasi siswa">
                <a class="student-nav-item" href="{{ route('dashboard') }}"><span class="student-nav-icon" aria-hidden="true">⌂</span>Dashboard</a>
                <a class="student-nav-item" href="{{ route('siswa.riwayat-kesehatan') }}"><span class="student-nav-icon" aria-hidden="true">▤</span>Riwayat kesehatan saya</a>
                <a class="student-nav-item is-active" href="{{ route('siswa.stok-obat') }}" aria-current="page"><span class="student-nav-icon" aria-hidden="true">＋</span>Cek stok obat</a>
            </nav>
            <div class="student-sidebar-bottom"><span class="student-status-dot"></span>SiUKS aktif</div>
        </aside>
        <main class="student-stock-main">
        <header class="student-stock-topbar">
            <button class="student-sidebar-toggle" type="button" data-student-sidebar-toggle aria-expanded="true" aria-label="Sembunyikan navigasi" title="Sembunyikan navigasi"><span aria-hidden="true">⋮</span></button>
            <div class="student-stock-context"><span>Portal Siswa</span><strong>Cek Stok Obat</strong></div>
            <div class="student-stock-account">
                <span class="student-stock-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                <span class="student-stock-name">{{ $user->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="student-stock-logout" type="submit">Logout</button>
                </form>
            </div>
        </header>

        <div class="student-stock-content">
            <section class="student-stock-heading">
                <div>
                    <p class="student-stock-eyebrow">Informasi UKS · Kelas {{ $student->kelas }}</p>
                    <h1>Cek Stok Obat</h1>
                    <p>Lihat ketersediaan obat di UKS sekolah.</p>
                </div>
                <a class="student-stock-back" href="{{ route('dashboard') }}">← Kembali ke dashboard</a>
            </section>

            <section class="student-stock-summary" aria-label="Ringkasan stok obat">
                <article><span class="student-stock-summary-icon" aria-hidden="true">▤</span><div><span>Jenis obat</span><strong>{{ $medicines->count() }}</strong></div></article>
                <article><span class="student-stock-summary-icon is-safe" aria-hidden="true">✓</span><div><span>Stok aman</span><strong>{{ $medicines->count() - $criticalMedicineCount }}</strong></div></article>
                <article><span class="student-stock-summary-icon is-critical" aria-hidden="true">!</span><div><span>Stok kritis</span><strong>{{ $criticalMedicineCount }}</strong></div></article>
            </section>

            <section class="student-stock-panel" aria-labelledby="student-stock-table-title">
                <div class="student-stock-panel-heading">
                    <div>
                        <h2 id="student-stock-table-title">Daftar ketersediaan obat</h2>
                        <p>Status kritis menandakan stok {{ $criticalMedicineThreshold }} atau kurang.</p>
                    </div>
                    <span class="student-stock-count">{{ $medicines->count() }} obat</span>
                </div>
                <div class="student-stock-table-wrap">
                    <table class="student-stock-table">
                        <thead>
                            <tr>
                                <th scope="col">Nama obat</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Stok tersedia</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($medicines as $medicine)
                                @php($isCritical = $medicine->stok_tersedia <= $criticalMedicineThreshold)
                                <tr>
                                    <td><strong>{{ $medicine->nama_obat }}</strong></td>
                                    <td>{{ $medicine->kategori }}</td>
                                    <td><strong>{{ $medicine->stok_tersedia }}</strong> {{ $medicine->satuan }}</td>
                                    <td><span class="student-stock-status {{ $isCritical ? 'is-critical' : 'is-safe' }}"><i aria-hidden="true"></i>{{ $isCritical ? 'Kritis' : 'Aman' }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="student-stock-empty">Belum ada data stok obat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="student-stock-note">Informasi ini hanya menunjukkan ketersediaan. Untuk penggunaan obat, silakan konsultasikan dengan petugas UKS.</p>
            </section>
            <footer class="student-stock-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data stok mengikuti catatan UKS</span></footer>
        </div>
        </main>
    </div>
</body>
</html>
