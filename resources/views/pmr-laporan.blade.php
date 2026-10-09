<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#EEF4FB">
    <title>Cetak Laporan | SiUKS</title>
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
                <div class="pmr-topbar-title"><span>Portal Petugas PMR</span><strong>Cetak laporan</strong></div>
                <div class="pmr-account">
                    <span class="pmr-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <span class="pmr-account-name">{{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="pmr-logout" type="submit">Logout</button></form>
                </div>
            </header>

            <div class="pmr-content pmr-report-content">
                <section class="pmr-page-heading">
                    <div><p>Dokumen UKS</p><h1>Cetak Laporan</h1><span>Pilih jenis laporan dan periode untuk mengunduh dokumen PDF.</span></div>
                </section>

                @if ($errors->any())
                    <div class="pmr-form-errors" role="alert">
                        <strong>Periode laporan tidak valid.</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form class="pmr-report-form" method="GET" id="pmr-report-form">
                    <section class="pmr-report-section" aria-labelledby="pmr-report-types-title">
                        <div class="pmr-report-section-heading">
                            <span class="pmr-form-step">01</span>
                            <div><h2 id="pmr-report-types-title">Pilih jenis laporan</h2><p>Pilih satu laporan yang ingin dibuat.</p></div>
                        </div>
                        <div class="pmr-report-cards">
                            @foreach ($reports as $type => $title)
                                <label class="pmr-report-card {{ $type === 'medical' ? 'is-selected' : '' }}">
                                    <input type="radio" name="report_type" value="{{ $type }}" @checked($type === 'medical') required>
                                    <span class="pmr-report-card-icon" aria-hidden="true">
                                        @switch($type)
                                            @case('patients') ♙ @break
                                            @case('stock') ▤ @break
                                            @case('medical') ▣ @break
                                            @case('visits') ▦ @break
                                        @endswitch
                                    </span>
                                    <span class="pmr-report-card-copy"><strong>{{ $title }}</strong><small>
                                        @switch($type)
                                            @case('patients') Ringkasan siswa yang berkunjung dan jumlah kunjungannya. @break
                                            @case('stock') Daftar obat, kategori, stok tersedia, dan status stok. @break
                                            @case('medical') Rincian catatan pemeriksaan dan penanganan siswa. @break
                                            @case('visits') Total kunjungan harian dan jumlah siswa unik. @break
                                        @endswitch
                                    </small></span>
                                    <span class="pmr-report-radio-mark" aria-hidden="true"></span>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <section class="pmr-report-period" aria-labelledby="pmr-report-period-title">
                        <div class="pmr-report-section-heading">
                            <span class="pmr-form-step">02</span>
                            <div><h2 id="pmr-report-period-title">Tentukan periode laporan</h2><p>Filter tanggal diterapkan pada laporan pasien, rekam medis, dan kunjungan.</p></div>
                        </div>
                        <div class="pmr-report-period-fields">
                            <div class="pmr-field"><label for="report-date-from">Dari tanggal</label><input id="report-date-from" name="date_from" type="date" value="{{ request('date_from', now()->startOfMonth()->toDateString()) }}"></div>
                            <div class="pmr-report-period-separator" aria-hidden="true">sampai</div>
                            <div class="pmr-field"><label for="report-date-to">Sampai tanggal</label><input id="report-date-to" name="date_to" type="date" value="{{ request('date_to', now()->toDateString()) }}"></div>
                        </div>
                        <p class="pmr-report-note">Laporan stok menunjukkan snapshot persediaan saat PDF dibuat; pilihan tanggal tidak mengubah data stok historis.</p>
                        <button class="pmr-report-download" type="submit"><span aria-hidden="true">↓</span>Unduh laporan PDF</button>
                    </section>
                </form>
                <footer class="pmr-footer">SiUKS · Sistem Informasi Kesehatan Sekolah <span>Dokumen laporan dibuat dari data UKS tersimpan</span></footer>
            </div>
        </main>
    </div>
    <script>
        const reportForm = document.querySelector('#pmr-report-form');
        const reportTypes = document.querySelectorAll('input[name="report_type"]');
        reportTypes.forEach((radio) => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('.pmr-report-card').forEach((card) => card.classList.remove('is-selected'));
                radio.closest('.pmr-report-card').classList.add('is-selected');
            });
        });
        reportForm.addEventListener('submit', (event) => {
            const selectedReport = reportForm.querySelector('input[name="report_type"]:checked');
            if (!selectedReport) {
                event.preventDefault();
                return;
            }
            reportForm.action = `/pmr/laporan/${selectedReport.value}/pdf`;
        });
    </script>
</body>
</html>
