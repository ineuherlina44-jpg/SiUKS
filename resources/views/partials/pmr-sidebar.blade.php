<aside class="pmr-sidebar">
    <a class="pmr-brand" href="{{ route('dashboard') }}" aria-label="SiUKS dashboard Petugas PMR">
        <img src="{{ asset('images/siuks-logo.png.png') }}" alt="">
        <span><strong>SiUKS</strong><small>Portal Petugas PMR</small></span>
    </a>
    <nav class="pmr-navigation" aria-label="Navigasi Petugas PMR">
        <a class="pmr-nav-item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><span aria-hidden="true">⌂</span>Dashboard</a>
        <a class="pmr-nav-item {{ request()->routeIs('pmr.rekam-medis.create') ? 'is-active' : '' }}" href="{{ route('pmr.rekam-medis.create') }}"><span aria-hidden="true">▤</span>Catat pasien sakit</a>
        <a class="pmr-nav-item {{ request()->routeIs('pmr.obat.*') ? 'is-active' : '' }}" href="{{ route('pmr.obat.index') }}"><span aria-hidden="true">＋</span>Kelola stok obat</a>
        <a class="pmr-nav-item {{ request()->routeIs('pmr.rekam-medis.index') ? 'is-active' : '' }}" href="{{ route('pmr.rekam-medis.index') }}"><span aria-hidden="true">▣</span>Riwayat rekam medis</a>
        <a class="pmr-nav-item {{ request()->routeIs('pmr.laporan.*') ? 'is-active' : '' }}" href="{{ route('pmr.laporan.index') }}"><span aria-hidden="true">▤</span>Cetak laporan</a>
    </nav>
    <div class="pmr-sidebar-bottom"><span></span>PMR Unit Sekolah</div>
</aside>
