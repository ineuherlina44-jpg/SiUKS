<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#EAF3FC">
    <title>Riwayat Kesehatan Saya | SiUKS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="student-history-page">
    <div class="student-history-app">
        <aside class="student-sidebar">
            <a class="student-brand" href="{{ route('dashboard') }}" aria-label="SiUKS dashboard siswa">
                <img src="{{ asset('images/siuks-logo.png.png') }}" alt="">
                <span><strong>SiUKS</strong><small>Portal Siswa</small></span>
            </a>
            <nav class="student-navigation" aria-label="Navigasi siswa">
                <a class="student-nav-item" href="{{ route('dashboard') }}"><span class="student-nav-icon" aria-hidden="true">⌂</span>Dashboard</a>
                <a class="student-nav-item is-active" href="{{ route('siswa.riwayat-kesehatan') }}" aria-current="page"><span class="student-nav-icon" aria-hidden="true">▤</span>Riwayat kesehatan saya</a>
                <a class="student-nav-item" href="{{ route('siswa.stok-obat') }}"><span class="student-nav-icon" aria-hidden="true">＋</span>Cek stok obat</a>
            </nav>
            <div class="student-sidebar-bottom"><span class="student-status-dot"></span>SiUKS aktif</div>
        </aside>
        <main class="student-history-main">
        <header class="student-stock-topbar">
            <button class="student-sidebar-toggle" type="button" data-student-sidebar-toggle aria-expanded="true" aria-label="Sembunyikan navigasi" title="Sembunyikan navigasi"><span aria-hidden="true">⋮</span></button>
            <div class="student-stock-context"><span>Portal Siswa</span><strong>Riwayat kesehatan saya</strong></div>
            <div class="student-stock-account">
                <span class="student-stock-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                <span class="student-stock-name">{{ $user->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="student-stock-logout" type="submit">Logout</button>
                </form>
            </div>
        </header>

        <div class="student-history-content">
            <section class="student-history-heading">
                <div>
                    <p class="student-stock-eyebrow">Informasi UKS · Kelas {{ $student->kelas }}</p>
                    <h1>Riwayat Kesehatan Saya</h1>
                    <p>Ringkasan kunjungan UKS dan catatan kesehatanmu.</p>
                </div>
                <a class="student-stock-back" href="{{ route('dashboard') }}">← Kembali ke dashboard</a>
            </section>

            <section class="student-stock-summary" aria-label="Ringkasan kesehatan">
                <article><span class="student-stock-summary-icon" aria-hidden="true">＋</span><div><span>Total kunjungan</span><strong>{{ $totalVisits }}</strong></div></article>
                <article><span class="student-stock-summary-icon is-safe" aria-hidden="true">▦</span><div><span>Kunjungan bulan ini</span><strong>{{ $monthlyVisits }}</strong></div></article>
                <article><span class="student-stock-summary-icon is-critical" aria-hidden="true">!</span><div><span>SOS dikirim</span><strong>{{ $totalAlerts }}</strong></div></article>
            </section>

            <section class="student-stock-panel" aria-labelledby="student-history-table-title">
                <div class="student-stock-panel-heading">
                    <div>
                        <h2 id="student-history-table-title">Riwayat kunjungan UKS</h2>
                        <p>Semua catatan kesehatan yang tercatat di UKS.</p>
                    </div>
                    <span class="student-stock-count">{{ $records->count() }} kunjungan</span>
                </div>
                <div class="student-stock-table-wrap">
                    <table class="student-stock-table student-history-table">
                        <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Keluhan</th>
                                <th scope="col">Obat</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $record)
                                @php
                                    $statusLabel = match ($record->status) {
                                        'diizinkan_pulang' => 'Pulang',
                                        'dirawat' => 'Dirawat',
                                        'rujukan' => 'Rujukan',
                                        default => ucfirst($record->status),
                                    };
                                    $medicines = $recordMedicines->get($record->id, collect())->pluck('nama_obat')->unique()->join(', ');
                                @endphp
                                <tr>
                                    <td>{{ \Illuminate\Support\Carbon::parse($record->waktu_pemeriksaan)->format('d M Y') }}</td>
                                    <td>{{ $record->keluhan ?: 'Tidak dicatat' }}</td>
                                    <td>{{ $medicines ?: '-' }}</td>
                                    <td><span class="student-history-status {{ $record->status === 'diizinkan_pulang' ? 'is-complete' : ($record->status === 'dirawat' ? 'is-in-care' : 'is-referral') }}">{{ $statusLabel }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="student-stock-empty">Belum ada riwayat kunjungan UKS.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="student-stock-note">Jika ada informasi yang kurang tepat, silakan hubungi petugas UKS.</p>
            </section>
            <footer class="student-stock-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Data mengikuti catatan UKS</span></footer>
        </div>
        </main>
    </div>
</body>
</html>
