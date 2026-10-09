<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FFFFFF">
    <title>SiUKS | Sistem Informasi Kesehatan Sekolah</title>
    <style>
        :root { font-family: Inter, "Segoe UI", sans-serif; color: #17324D; background: #FFFFFF; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { min-width: 320px; margin: 0; background: #FFFFFF; }
        a { color: inherit; text-decoration: none; }
        .site-header { position: sticky; z-index: 5; top: 0; display: flex; min-height: 72px; align-items: center; justify-content: space-between; gap: 24px; padding: 10px max(24px, calc((100vw - 1120px) / 2)); border-bottom: 1px solid #E8EDF2; background: #FFFFFF; }
        .site-brand { display: flex; min-width: 180px; align-items: center; gap: 10px; }
        .site-brand img { width: 46px; height: 46px; object-fit: contain; }
        .site-brand-copy { display: grid; gap: 2px; }
        .site-brand-copy strong { color: #D62839; font-size: 16px; }
        .site-brand-copy span { color: #52677A; font-size: 10px; }
        .main-nav { display: flex; align-items: center; gap: 22px; color: #17324D; font-size: 12px; }
        .main-nav a:not(.nav-register):hover { color: #D62839; }
        .nav-actions { display: flex; align-items: center; gap: 9px; }
        .nav-login, .nav-register { display: inline-flex; min-height: 37px; align-items: center; justify-content: center; padding: 0 15px; border: 1px solid #D62839; border-radius: 5px; font-size: 11px; font-weight: 700; }
        .nav-login { color: #D62839; background: #FFFFFF; }
        .nav-register { color: #FFFFFF; background: #D62839; }
        .nav-register:hover, .button-primary:hover { background: #B91F31; }
        .hero { display: grid; min-height: 440px; grid-template-columns: 1.05fr .95fr; align-items: center; gap: 40px; padding: 58px max(24px, calc((100vw - 1120px) / 2)); background: #EAF3FF; }
        .hero-copy { max-width: 520px; }
        .hero-kicker { display: inline-flex; align-items: center; gap: 7px; margin-bottom: 14px; color: #D62839; font-size: 11px; font-weight: 700; }
        .hero-kicker::before { width: 18px; height: 2px; background: #D62839; content: ""; }
        .hero h1 { max-width: 520px; margin: 0; color: #17324D; font-size: clamp(34px, 5vw, 54px); line-height: 1.05; }
        .hero h1 span { color: #D62839; }
        .hero-copy p { max-width: 470px; margin: 18px 0 24px; color: #52677A; font-size: 14px; line-height: 1.7; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .button-primary, .button-secondary { display: inline-flex; min-height: 43px; align-items: center; justify-content: center; padding: 0 17px; border: 1px solid #D62839; border-radius: 5px; font-size: 12px; font-weight: 700; transition: background .16s, color .16s; }
        .button-primary { color: #FFFFFF; background: #D62839; }
        .button-secondary { color: #D62839; background: #FFFFFF; }
        .button-secondary:hover { background: #FDE7EA; }
        .hero-art { display: grid; min-height: 300px; place-items: center; overflow: hidden; }
        .hero-art img { width: min(340px, 82%); max-height: 340px; object-fit: contain; filter: drop-shadow(0 16px 20px #17324D22); }
        .section { padding: 64px 24px; }
        .section-inner { width: min(1050px, 100%); margin: 0 auto; }
        .section-heading { max-width: 620px; margin: 0 auto 28px; text-align: center; }
        .section-heading span { color: #D62839; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .section-heading h2 { margin: 8px 0; color: #17324D; font-size: 28px; line-height: 1.2; }
        .section-heading p { margin: 0; color: #52677A; font-size: 12px; line-height: 1.65; }
        .problem-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .problem-item { display: grid; grid-template-columns: 42px 1fr; gap: 12px; padding: 19px; border: 1px solid #DCE6F0; border-radius: 6px; background: #FFFFFF; }
        .problem-icon { display: grid; width: 38px; height: 38px; place-items: center; color: #D62839; border-radius: 5px; background: #FDE7EA; font-size: 17px; font-weight: 700; }
        .problem-item h3 { margin: 1px 0 6px; color: #17324D; font-size: 14px; }
        .problem-item p { margin: 0; color: #52677A; font-size: 11px; line-height: 1.6; }
        .about { background: #FDE7EA; }
        .about-inner { display: grid; grid-template-columns: .7fr 1.3fr; align-items: center; gap: 34px; width: min(1000px, 100%); margin: 0 auto; }
        .about-mark { display: grid; min-height: 190px; place-items: center; }
        .about-mark img { width: min(190px, 70%); max-height: 190px; object-fit: contain; }
        .about-copy h2 { margin: 0 0 12px; color: #17324D; font-size: 27px; }
        .about-copy p { margin: 0; color: #435D73; font-size: 13px; line-height: 1.8; }
        .reviews { display: grid; grid-template-columns: .75fr 1.25fr; align-items: center; gap: 30px; }
        .rating-summary { padding: 22px; border: 1px solid #F0D1D5; border-radius: 6px; background: #FFFFFF; text-align: center; }
        .rating-summary strong { display: block; color: #17324D; font-size: 39px; line-height: 1; }
        .rating-stars { margin: 10px 0 7px; color: #F4A340; font-size: 20px; letter-spacing: 2px; }
        .rating-summary p, .review-empty { margin: 0; color: #52677A; font-size: 11px; line-height: 1.6; }
        .review-empty { padding: 22px; border-left: 3px solid #D62839; background: #EAF3FF; }
        .faq { background: #F7FAFD; }
        .faq-list { width: min(780px, 100%); margin: 0 auto; border-top: 1px solid #DCE6F0; }
        .faq-list details { border-bottom: 1px solid #DCE6F0; }
        .faq-list summary { position: relative; padding: 17px 36px 17px 2px; color: #17324D; cursor: pointer; font-size: 13px; font-weight: 600; list-style: none; }
        .faq-list summary::-webkit-details-marker { display: none; }
        .faq-list summary::after { position: absolute; top: 14px; right: 4px; color: #D62839; content: "+"; font-size: 19px; }
        .faq-list details[open] summary::after { content: "-"; }
        .faq-list details p { max-width: 680px; padding: 0 20px 17px 2px; margin: 0; color: #52677A; font-size: 12px; line-height: 1.7; }
        .site-footer { padding: 36px 24px 20px; color: #FFFFFF; background: #17324D; }
        .footer-inner { display: grid; grid-template-columns: 1fr auto; gap: 24px; width: min(1050px, 100%); margin: 0 auto; }
        .footer-brand { display: flex; align-items: center; gap: 10px; }
        .footer-brand img { width: 42px; height: 42px; object-fit: contain; border-radius: 50%; background: #FFFFFF; }
        .footer-brand strong { display: block; font-size: 14px; }.footer-brand span { display: block; margin-top: 3px; color: #DCE7F1; font-size: 10px; }
        .footer-social { display: flex; align-items: center; gap: 9px; color: #EAF3FF; font-size: 11px; }
        .footer-social span:last-child { color: #FFFFFF; }
        .footer-bottom { display: flex; justify-content: space-between; gap: 16px; padding-top: 18px; margin-top: 24px; color: #DCE7F1; border-top: 1px solid #ffffff33; font-size: 10px; }
        @media (max-width: 760px) { .site-header { min-height: 62px; gap: 10px; padding: 8px 16px; }.site-brand { min-width: 0; }.site-brand img { width: 38px; height: 38px; }.site-brand-copy span { display: none; }.main-nav { display: none; }.nav-actions { margin-left: auto; }.nav-login, .nav-register { min-height: 34px; padding: 0 11px; font-size: 10px; }.hero { min-height: 0; grid-template-columns: 1fr; gap: 12px; padding: 42px 22px 30px; }.hero h1 { font-size: 38px; }.hero-copy p { font-size: 12px; }.hero-art { min-height: 190px; }.hero-art img { width: 160px; max-height: 170px; }.section { padding: 46px 20px; }.section-heading h2 { font-size: 24px; }.problem-grid { grid-template-columns: 1fr; }.about-inner { grid-template-columns: 1fr; gap: 8px; }.about-mark { min-height: 120px; }.about-mark img { width: 115px; max-height: 115px; }.about-copy h2 { font-size: 23px; }.reviews { grid-template-columns: 1fr; gap: 13px; }.rating-summary { padding: 18px; }.footer-inner { grid-template-columns: 1fr; }.footer-social { justify-content: flex-start; }.footer-bottom { flex-direction: column; } }
        @media (max-width: 400px) { .site-header { padding-inline: 10px; }.site-brand-copy strong { font-size: 13px; }.nav-actions { gap: 5px; }.nav-login, .nav-register { padding: 0 8px; }.hero h1 { font-size: 33px; }.hero-actions { display: grid; grid-template-columns: 1fr 1fr; }.button-primary, .button-secondary { padding: 0 9px; font-size: 10px; } }
    </style>
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}" aria-label="SiUKS Beranda"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""><span class="site-brand-copy"><strong>SiUKS</strong><span>Sistem Informasi Kesehatan Sekolah</span></span></a>
        <nav class="main-nav" aria-label="Navigasi halaman"><a href="#masalah-solusi">Masalah &amp; Solusi</a><a href="#tentang">Tentang</a><a href="#ulasan">Ulasan</a><a href="#faq">FAQ</a></nav>
        <div class="nav-actions"><a class="nav-login" href="{{ route('login') }}">Masuk</a><a class="nav-register" href="{{ route('register') }}">Daftar</a></div>
    </header>

    <main>
        <section class="hero" aria-labelledby="hero-title">
            <div class="hero-copy"><span class="hero-kicker">PMR dan kesehatan sekolah</span><h1 id="hero-title">SiUKS untuk sekolah <span>lebih sehat.</span></h1><p>Satu ruang untuk mendukung layanan UKS, kegiatan PMR, dan kepedulian kesehatan di lingkungan sekolah.</p><div class="hero-actions"><a class="button-primary" href="{{ route('register') }}">Daftar sekarang</a><a class="button-secondary" href="{{ route('login') }}">Masuk ke SiUKS</a></div></div>
            <div class="hero-art"><img src="{{ asset('images/siuks-logo.png.png') }}" alt="Logo SiUKS"></div>
        </section>

        <section class="section" id="masalah-solusi">
            <div class="section-inner"><div class="section-heading"><span>Masalah &amp; Solusi</span><h2>Dukungan kesehatan sekolah yang lebih teratur</h2><p>SiUKS membantu sekolah menghubungkan pencatatan UKS, koordinasi PMR, dan informasi kesehatan.</p></div>
                <div class="problem-grid">
                    <article class="problem-item"><span class="problem-icon" aria-hidden="true">!</span><div><h3>Informasi tersebar</h3><p>Catatan kegiatan dan layanan kesehatan sering berada di tempat yang berbeda.</p></div></article>
                    <article class="problem-item"><span class="problem-icon" aria-hidden="true">+</span><div><h3>Pencatatan lebih terpusat</h3><p>SiUKS menyediakan satu ruang untuk mengelola data dan aktivitas UKS.</p></div></article>
                    <article class="problem-item"><span class="problem-icon" aria-hidden="true">?</span><div><h3>Koordinasi sulit dipantau</h3><p>Jadwal, tindak lanjut, dan informasi PMR perlu mudah dilihat bersama.</p></div></article>
                    <article class="problem-item"><span class="problem-icon" aria-hidden="true">✓</span><div><h3>Peran lebih terhubung</h3><p>Siswa, petugas PMR, wali kelas, dan pembina dapat memakai akses sesuai perannya.</p></div></article>
                </div>
            </div>
        </section>

        <section class="section about" id="tentang">
            <div class="about-inner"><div class="about-mark"><img src="{{ asset('images/siuks-logo.png.png') }}" alt="Logo SiUKS"></div><div class="about-copy"><span class="hero-kicker">Tentang SiUKS</span><h2>Informasi UKS dalam satu sistem</h2><p>SiUKS adalah sistem informasi kesehatan sekolah untuk mendukung pendataan, layanan UKS, kegiatan Palang Merah Remaja, serta koordinasi warga sekolah. Informasi yang disajikan dapat disesuaikan dengan kebutuhan sekolah.</p></div></div>
        </section>

        <section class="section" id="ulasan">
            <div class="section-inner"><div class="section-heading"><span>Rating &amp; Ulasan</span><h2>Pengalaman pengguna SiUKS</h2></div>
                <div class="reviews"><div class="rating-summary"><strong>--</strong><div class="rating-stars" aria-label="Belum ada penilaian">☆☆☆☆☆</div><p>Belum ada penilaian</p></div><p class="review-empty">Belum ada ulasan yang ditampilkan.</p></div>
            </div>
        </section>

        <section class="section faq" id="faq">
            <div class="section-inner"><div class="section-heading"><span>FAQ</span><h2>Pertanyaan yang sering diajukan</h2></div>
                <div class="faq-list">
                    <details><summary>Apa itu SiUKS?</summary><p>SiUKS adalah sistem informasi untuk mendukung pendataan dan layanan kesehatan di lingkungan sekolah.</p></details>
                    <details><summary>Siapa yang dapat menggunakan SiUKS?</summary><p>Siswa, petugas PMR, wali kelas, dan Admin atau pembina dapat menggunakan akses sesuai peran masing-masing.</p></details>
                    <details><summary>Bagaimana cara membuat akun?</summary><p>Pilih tombol Daftar, tentukan kategori akun, lalu lengkapi data yang diminta pada formulir.</p></details>
                    <details><summary>Bagaimana cara masuk?</summary><p>Pilih tombol Masuk, tentukan peran, lalu gunakan NISN untuk siswa atau email untuk peran lain beserta password.</p></details>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner"><div class="footer-brand"><img src="{{ asset('images/siuks-logo.png.png') }}" alt=""><div><strong>SiUKS</strong><span>Sistem Informasi Kesehatan Sekolah</span></div></div><div class="footer-social" aria-label="Media sosial"><span>Media sosial</span><span>Instagram / WhatsApp / Email</span></div></div>
        <div class="footer-bottom"><span>© 2026 SiUKS</span><span>Hak cipta dilindungi</span></div>
    </footer>
</body>
</html>