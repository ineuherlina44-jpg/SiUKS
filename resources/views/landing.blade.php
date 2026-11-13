<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FFFFFF">
    <title>SiUKS | Sistem Informasi Kesehatan Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #D62839;
            --primary-gradient: linear-gradient(135deg, #E63946 0%, #B91F31 100%);
            --primary-hover: #B91F31;
            --primary-soft: #FDE7EA;
            --text-dark: #17324D;
            --text-muted: #52677A;
            --bg-light: #F8FAFC;
            --border-color: #E2E8F0;
            --font-main: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --shadow-sm: 0 4px 12px rgba(214, 40, 57, 0.15);
            --shadow-lg: 0 12px 30px rgba(23, 50, 77, 0.08);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            width: 100%;
            min-width: 320px;
            margin: 0;
            background: #FFFFFF;
            font-family: var(--font-main);
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        a { color: inherit; text-decoration: none; }

        /* Site Header */
        .site-header {
            position: sticky;
            z-index: 100;
            top: 0;
            display: flex;
            min-height: 76px;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 5%;
            border-bottom: 1px solid var(--border-color);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
        }
        .site-brand { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .site-brand img { width: 42px; height: 42px; object-fit: contain; transition: transform 0.3s ease; }
        .site-brand:hover img { transform: scale(1.08); }
        .site-brand-copy { display: grid; gap: 2px; }
        .site-brand-copy strong { color: var(--primary); font-size: 16px; font-weight: 800; letter-spacing: -0.3px; }
        .site-brand-copy span { color: var(--text-muted); font-size: 10px; font-weight: 500; white-space: nowrap; }

        .main-nav { display: flex; align-items: center; gap: clamp(14px, 2vw, 28px); color: var(--text-dark); font-size: 13px; font-weight: 600; }
        .main-nav a { position: relative; transition: color 0.2s ease; white-space: nowrap; }
        .main-nav a:hover { color: var(--primary); }
        .main-nav a:focus-visible, .site-nav-toggle:focus-visible { outline: 3px solid rgba(214, 40, 57, 0.25); outline-offset: 3px; }

        .nav-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .site-nav-toggle {
            display: none;
            width: 40px;
            height: 40px;
            flex: 0 0 auto;
            place-items: center;
            color: var(--text-dark);
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            font-family: var(--font-main);
            font-size: 20px;
        }
        .nav-login, .nav-register {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        main section[id] { scroll-margin-top: 90px; }
        .nav-login { color: var(--primary); background: #FFFFFF; border: 1.5px solid var(--border-color); }
        .nav-login:hover { border-color: var(--primary); background: var(--primary-soft); transform: translateY(-2px); }
        .nav-register { color: #FFFFFF; background: var(--primary-gradient); border: none; box-shadow: var(--shadow-sm); }
        .nav-register:hover { transform: translateY(-2px); filter: brightness(1.05); }

        /* Hero Section */
        .hero {
            display: grid;
            min-height: 480px;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 30px;
            padding: 60px 5%;
            background: radial-gradient(circle at top right, rgba(234, 243, 255, 0.8) 0%, rgba(248, 250, 252, 1) 70%);
        }
        .hero-copy { max-width: 540px; }
        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            background: var(--primary-soft);
            padding: 5px 12px;
            border-radius: 20px;
        }
        .hero h1 {
            margin: 0;
            color: var(--text-dark);
            font-size: clamp(30px, 4.5vw, 52px);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
        }
        .hero h1 span { background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-copy p { margin: 16px 0 24px; color: var(--text-muted); font-size: clamp(13px, 1.5vw, 15px); line-height: 1.7; }

        .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .button-primary, .button-secondary {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .button-primary { color: #FFFFFF; background: var(--primary-gradient); border: none; box-shadow: 0 6px 20px rgba(214, 40, 57, 0.3); }
        .button-primary:hover { transform: translateY(-2px); filter: brightness(1.05); }
        .button-secondary { color: var(--text-dark); background: #FFFFFF; border: 1.5px solid var(--border-color); }
        .button-secondary:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-soft); transform: translateY(-2px); }

        .hero-art { display: grid; place-items: center; }
        .hero-art img { width: 100%; max-width: 320px; height: auto; object-fit: contain; filter: drop-shadow(0 20px 30px rgba(23, 50, 77, 0.15)); animation: float 4s ease-in-out infinite; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Responsive Media Query agar HP Tetap Proporsional */
        @media (max-width: 768px) {
            .main-nav { display: none; }
            .site-header.is-nav-open .main-nav {
                position: absolute;
                top: 100%;
                right: 0;
                left: 0;
                display: grid;
                gap: 0;
                padding: 8px 5% 14px;
                background: #FFFFFF;
                border-bottom: 1px solid var(--border-color);
                box-shadow: 0 14px 22px rgba(23, 50, 77, 0.08);
            }
            .main-nav a { padding: 12px 4px; }
            .site-nav-toggle { display: inline-grid; }
            .hero { grid-template-columns: 1fr; text-align: center; padding: 40px 5%; gap: 24px; }
            .hero-copy { margin: 0 auto; }
            .hero-kicker { margin: 0 auto 12px; }
            .hero-actions { justify-content: center; }
        }
        @media (max-width: 480px) {
            .site-header { gap: 8px; padding: 10px 4%; }
            .site-brand { gap: 6px; }
            .site-brand img { width: 34px; height: 34px; }
            .site-brand-copy span { display: none; }
            .nav-actions { gap: 5px; }
            .nav-login, .nav-register { min-height: 34px; padding: 0 9px; font-size: 11px; }
            .site-nav-toggle { width: 34px; height: 34px; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}" aria-label="SiUKS Beranda">
            <img src="{{ asset('images/siuks-logo.png.png') }}" alt="Logo">
            <span class="site-brand-copy">
                <strong>SiUKS</strong>
                <span>Sistem Informasi Kesehatan</span>
            </span>
        </a>
        <nav class="main-nav" id="main-nav" aria-label="Navigasi">
            <a href="#masalah-solusi">Masalah &amp; Solusi</a>
            <a href="#tentang">Tentang</a>
            <a href="#faq">FAQ</a>
        </nav>
        <button class="site-nav-toggle" type="button" aria-controls="main-nav" aria-expanded="false" aria-label="Buka navigasi">☰</button>
        <div class="nav-actions">
            <a class="nav-login" href="{{ route('login') }}">Masuk</a>
            <a class="nav-register" href="{{ route('register') }}">Daftar</a>
        </div>
    </header>

    <main>
        <section class="hero" aria-labelledby="hero-title">
            <div class="hero-copy">
                <span class="hero-kicker">PMR dan kesehatan sekolah</span>
                <h1 id="hero-title">SiUKS untuk sekolah <span>lebih sehat.</span></h1>
                <p>Satu ruang terpadu untuk mendukung layanan UKS, pengelolaan kegiatan PMR, dan kepedulian kesehatan di lingkungan sekolah.</p>
                <div class="hero-actions">
                    <a class="button-primary" href="{{ route('register') }}">Daftar sekarang</a>
                    <a class="button-secondary" href="{{ route('login') }}">Masuk ke SiUKS</a>
                </div>
            </div>
            <div class="hero-art">
                <img src="{{ asset('images/siuks-logo.png.png') }}" alt="Logo SiUKS">
            </div>
        </section>

        <section class="section" id="masalah-solusi" style="padding: 70px 5%;">
            <div class="section-inner" style="width: min(1050px, 100%); margin: 0 auto;">
                <div class="section-heading" style="max-width: 600px; margin: 0 auto 36px; text-align: center;">
                    <span style="color: var(--primary); font-size: 11px; font-weight: 800; text-transform: uppercase; background: var(--primary-soft); padding: 5px 12px; border-radius: 20px; display: inline-block; margin-bottom: 8px;">Masalah &amp; Solusi</span>
                    <h2 style="margin: 8px 0 12px; color: var(--text-dark); font-size: clamp(24px, 3vw, 32px); font-weight: 800;">Dukungan kesehatan sekolah teratur</h2>
                    <p style="margin: 0; color: var(--text-muted); font-size: 13px; line-height: 1.6;">SiUKS menghubungkan pencatatan UKS, koordinasi PMR, dan informasi kesehatan dalam satu sistem.</p>
                </div>
                <div class="problem-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                    <article style="display: grid; grid-template-columns: 44px 1fr; gap: 14px; padding: 20px; border: 1px solid var(--border-color); border-radius: 14px; background: #FFFFFF; box-shadow: var(--shadow-lg);">
                        <div style="display: grid; width: 42px; height: 42px; place-items: center; color: #FFFFFF; border-radius: 10px; background: var(--primary-gradient); font-weight: 800;">!</div>
                        <div>
                            <h3 style="margin: 0 0 6px; color: var(--text-dark); font-size: 15px; font-weight: 700;">Informasi Tersebar</h3>
                            <p style="margin: 0; color: var(--text-muted); font-size: 12px; line-height: 1.6;">Catatan kegiatan dan data pertolongan seringkali terpisah.</p>
                        </div>
                    </article>
                    <article style="display: grid; grid-template-columns: 44px 1fr; gap: 14px; padding: 20px; border: 1px solid var(--border-color); border-radius: 14px; background: #FFFFFF; box-shadow: var(--shadow-lg);">
                        <div style="display: grid; width: 42px; height: 42px; place-items: center; color: #FFFFFF; border-radius: 10px; background: var(--primary-gradient); font-weight: 800;">+</div>
                        <div>
                            <h3 style="margin: 0 0 6px; color: var(--text-dark); font-size: 15px; font-weight: 700;">Pencatatan Terpusat</h3>
                            <p style="margin: 0; color: var(--text-muted); font-size: 12px; line-height: 1.6;">Satu dasbor terpadu untuk mengelola seluruh data aktivitas UKS.</p>
                        </div>
                    </article>
                    <article style="display: grid; grid-template-columns: 44px 1fr; gap: 14px; padding: 20px; border: 1px solid var(--border-color); border-radius: 14px; background: #FFFFFF; box-shadow: var(--shadow-lg);">
                        <div style="display: grid; width: 42px; height: 42px; place-items: center; color: #FFFFFF; border-radius: 10px; background: var(--primary-gradient); font-weight: 800;">?</div>
                        <div>
                            <h3 style="margin: 0 0 6px; color: var(--text-dark); font-size: 15px; font-weight: 700;">Koordinasi Dipantau</h3>
                            <p style="margin: 0; color: var(--text-muted); font-size: 12px; line-height: 1.6;">Jadwal piket dan tindak lanjut siswa sakit terpantau realtime.</p>
                        </div>
                    </article>
                    <article style="display: grid; grid-template-columns: 44px 1fr; gap: 14px; padding: 20px; border: 1px solid var(--border-color); border-radius: 14px; background: #FFFFFF; box-shadow: var(--shadow-lg);">
                        <div style="display: grid; width: 42px; height: 42px; place-items: center; color: #FFFFFF; border-radius: 10px; background: var(--primary-gradient); font-weight: 800;">✓</div>
                        <div>
                            <h3 style="margin: 0 0 6px; color: var(--text-dark); font-size: 15px; font-weight: 700;">Peran Terhubung</h3>
                            <p style="margin: 0; color: var(--text-muted); font-size: 12px; line-height: 1.6;">Siswa, PMR, dan pembina mengakses fitur sesuai wewenang.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section about" id="tentang" style="background: linear-gradient(135deg, #FDE7EA 0%, #FFF5F6 100%); padding: 70px 5%;">
            <div class="about-inner" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); align-items: center; gap: 30px; width: min(1000px, 100%); margin: 0 auto;">
                <div class="about-mark" style="text-align: center;"><img src="{{ asset('images/siuks-logo.png.png') }}" alt="Logo" style="width: 160px; max-height: 160px; object-fit: contain;"></div>
                <div class="about-copy">
                    <span style="color: var(--primary); font-size: 11px; font-weight: 800; text-transform: uppercase; background: #FFFFFF; padding: 5px 12px; border-radius: 20px; display: inline-block; margin-bottom: 8px;">Tentang SiUKS</span>
                    <h2 style="margin: 4px 0 12px; color: var(--text-dark); font-size: clamp(24px, 3vw, 30px); font-weight: 800;">Informasi UKS dalam satu sistem</h2>
                    <p style="margin: 0; color: #3A4E61; font-size: 13px; line-height: 1.7;">SiUKS adalah platform digital kesehatan sekolah untuk mempermudah pendataan, layanan kesehatan UKS, dan operasional PMR agar lebih efisien.</p>
                </div>
            </div>
        </section>

        <section class="section faq" id="faq" style="background: var(--bg-light); padding: 70px 5%;">
            <div class="section-inner" style="width: min(800px, 100%); margin: 0 auto;">
                <div class="section-heading" style="text-align: center; margin-bottom: 30px;">
                    <span style="color: var(--primary); font-size: 11px; font-weight: 800; text-transform: uppercase; background: var(--primary-soft); padding: 5px 12px; border-radius: 20px; display: inline-block; margin-bottom: 8px;">FAQ</span>
                    <h2 style="margin: 0; color: var(--text-dark); font-size: clamp(24px, 3vw, 30px); font-weight: 800;">Pertanyaan sering diajukan</h2>
                </div>
                <div class="faq-list">
                    <details style="border: 1px solid var(--border-color); border-radius: 12px; background: #FFFFFF; margin-bottom: 10px; overflow: hidden;">
                        <summary style="padding: 16px 20px; font-size: 14px; font-weight: 700; cursor: pointer;">Apa itu SiUKS?</summary>
                        <p style="padding: 0 20px 16px; margin: 0; font-size: 12px; color: var(--text-muted); line-height: 1.6;">Sistem informasi kesehatan sekolah untuk mendukung pendataan dan layanan UKS.</p>
                    </details>
                    <details style="border: 1px solid var(--border-color); border-radius: 12px; background: #FFFFFF; margin-bottom: 10px; overflow: hidden;">
                        <summary style="padding: 16px 20px; font-size: 14px; font-weight: 700; cursor: pointer;">Siapa yang dapat menggunakan?</summary>
                        <p style="padding: 0 20px 16px; margin: 0; font-size: 12px; color: var(--text-muted); line-height: 1.6;">Siswa, petugas PMR, wali kelas, dan pembina sesuai peran masing-masing.</p>
                    </details>
                    <details style="border: 1px solid var(--border-color); border-radius: 12px; background: #FFFFFF; margin-bottom: 10px; overflow: hidden;">
                        <summary style="padding: 16px 20px; font-size: 14px; font-weight: 700; cursor: pointer;">Bagaimana cara membuat akun?</summary>
                        <p style="padding: 0 20px 16px; margin: 0; font-size: 12px; color: var(--text-muted); line-height: 1.6;">Pilih tombol Daftar, tentukan kategori akun, lalu lengkapi formulir.</p>
                    </details>
                    <details style="border: 1px solid var(--border-color); border-radius: 12px; background: #FFFFFF; margin-bottom: 10px; overflow: hidden;">
                        <summary style="padding: 16px 20px; font-size: 14px; font-weight: 700; cursor: pointer;">Bagaimana cara masuk?</summary>
                        <p style="padding: 0 20px 16px; margin: 0; font-size: 12px; color: var(--text-muted); line-height: 1.6;">Pilih Masuk, gunakan NISN untuk siswa atau email untuk peran lain.</p>
                    </details>
                </div>
            </div>
        </section>
    </main>

    <footer style="padding: 40px 5% 20px; color: #FFFFFF; background: var(--text-dark);">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px; width: min(1050px, 100%); margin: 0 auto;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <img src="{{ asset('images/siuks-logo.png.png') }}" alt="" style="width: 40px; height: 40px; border-radius: 50%; background: #FFFFFF; padding: 2px;">
                <div>
                    <strong style="font-size: 15px;">SiUKS</strong>
                    <span style="display: block; color: #94A3B8; font-size: 10px;">Sistem Informasi Kesehatan Sekolah</span>
                </div>
            </div>
            <a href="https://www.instagram.com/pmr.smkbbc_?rpxt=MTNkcjVndnBrdHQ2cQ==" target="_blank" rel="noopener noreferrer" aria-label="Kunjungi Instagram SiUKS" style="color: #94A3B8; font-size: 11px; text-decoration: underline; text-underline-offset: 3px;">Instagram SiUKS</a>
        </div>
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px; width: min(1050px, 100%); margin: 30px auto 0; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); color: #64748B; font-size: 10px;">
            <span>© 2026 SiUKS. All rights reserved.</span>
            <span>Hak cipta dilindungi</span>
        </div>
    </footer>
    <script>
        const siteHeader = document.querySelector('.site-header');
        const navToggle = document.querySelector('.site-nav-toggle');

        const setNavigationOpen = (isOpen) => {
            siteHeader.classList.toggle('is-nav-open', isOpen);
            navToggle.setAttribute('aria-expanded', String(isOpen));
            navToggle.setAttribute('aria-label', isOpen ? 'Tutup navigasi' : 'Buka navigasi');
            navToggle.textContent = isOpen ? '×' : '☰';
        };

        navToggle.addEventListener('click', () => {
            setNavigationOpen(navToggle.getAttribute('aria-expanded') !== 'true');
        });

        siteHeader.querySelectorAll('.main-nav a').forEach((link) => {
            link.addEventListener('click', () => setNavigationOpen(false));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setNavigationOpen(false);
            }
        });
    </script>
</body>
</html>
