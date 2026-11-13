# Panduan Deploy SiUKS

## Platform yang dipilih: Railway

Railway cocok untuk proyek ini karena dapat mendeteksi Laravel/PHP dari `composer.json` dan `artisan`, membangun aset dari `package.json`, serta menyediakan layanan MySQL. Baca [panduan Laravel Railway](https://railway.com/guides/laravel), [panduan MySQL](https://docs.railway.com/databases/mysql), dan [dukungan PHP Railpack](https://railpack.com/languages/php).

1. Hubungkan repository GitHub ke Railway sebagai service Laravel.
2. Tambahkan service MySQL ke project yang sama.
3. Atur variabel service Laravel:

   ```text
   APP_NAME=SiUKS
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://<domain-railway-atau-domainmu>
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   LOG_CHANNEL=stderr
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database
   ```

   Ganti `MySQL` pada referensi variabel jika nama service database berbeda. Karena deployment ini mempertahankan instalasi yang sudah ada, gunakan `APP_KEY` dari instalasi aplikasi saat ini. Hanya buat key baru untuk instalasi yang benar-benar baru; jangan commit atau bagikan nilainya.
4. Pasang Railway Volume pada `/app/storage/app/private` agar foto rekam medis yang disimpan di private disk tetap ada setelah restart. Lihat [panduan Volume Railway](https://docs.railway.com/volumes).
5. Setelah deploy dan migrasi sukses, masuk menggunakan akun admin yang sudah ada pada database sumber. Jalankan `php artisan siuks:admin:create` hanya jika database memang belum memiliki admin; command tersebut sengaja menolak membuat admin kedua.

Railpack menjalankan aplikasi Laravel dengan document root `public/`; dukungan Laravel-nya juga mengoptimalkan aplikasi, membuat storage link, dan menjalankan migrasi saat startup secara default. Periksa deployment log untuk memastikan migrasi selesai. Jangan menambahkan perintah migrasi kedua tanpa alasan, dan jangan aktifkan `RAILPACK_SKIP_MIGRATIONS` untuk database baru.

### Memindahkan database MySQL yang sudah ada

Database yang sedang dipakai aplikasi berisi data yang perlu dipertahankan. Sebelum cutover:

1. Buat backup penuh database sumber dan simpan di lokasi lokal yang aman di luar repository. Gunakan tool MySQL yang meminta password secara interaktif; jangan menaruh password pada command, commit backup, atau mengirimkannya melalui chat.
2. Buat service MySQL Railway, lalu import backup ke database itu sebelum mengarahkan aplikasi ke sana.
3. Pertahankan `APP_KEY` instalasi lama. Jangan jalankan `php artisan migrate:fresh`, seeder demo, atau menghapus tabel `migrations`.
4. Deploy aplikasi dan pastikan migrasi yang masih pending selesai di log. Setelah itu, cocokkan jumlah record tabel utama dengan database sumber dan uji login sebelum mengalihkan pengguna.
5. Simpan backup sampai verifikasi selesai. Foto rekam medis baru disimpan di private disk; jika memindahkan berkas lama, pastikan berkasnya ada di volume private dan tidak tersisa di area publik.

Catatan skema: database sumber sudah mencatat migrasi `2026_10_09_132724_create_rooms_table.php` sebagai selesai. Mengubah file migrasi yang telah berjalan tidak mengubah tabel yang sudah ada. Kode aplikasi saat ini tidak memakai `rooms` atau `reservations`; jangan mengandalkan perubahan file tersebut untuk mengubah skema produksi. Jika fitur itu diperlukan nanti, buat migrasi baru setelah skema sumber diperiksa.

### Kredensial siswa

Akun siswa demo yang sudah ada dipertahankan apa adanya dan tidak dirotasi. Impor CSV siswa baru di production hanya menerima data sekolah; setiap akun baru mendapat password acak unik, ditampilkan satu kali kepada admin/wali kelas, dan wajib diganti pada login pertama. Roster otomatis yang tersedia untuk pengembangan dinonaktifkan di production.

## Konfigurasi server

- Gunakan PHP 8.2 atau lebih baru, Composer, dan Node.js yang didukung oleh dependensi proyek.
- Arahkan document root web server ke folder `public/`, bukan root repository.
- Simpan `.env` hanya di server. Atur `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` ke domain HTTPS, `APP_KEY`, serta koneksi database produksi.
- Gunakan database dan penyimpanan persisten yang memiliki backup. Konfigurasikan mailer produksi bila aplikasi perlu mengirim email.
- Pastikan `storage/` dan `bootstrap/cache/` dapat ditulis oleh proses PHP.

Jangan mengganti `APP_KEY` pada deploy berikutnya. Buat key sekali untuk instalasi baru dan simpan dengan aman.

## Instalasi dan pembaruan

Setelah `.env` dan database siap:

```sh
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Ambil backup database sebelum migrasi produksi. Jangan jalankan `migrate:fresh` pada database yang berisi data.

Periksa `php artisan migrate:status` pada database tujuan sebelum deploy. Repository saat ini mengubah file migrasi `2026_10_09_132724_create_rooms_table.php`, sedangkan migrasi tersebut sudah tercatat pernah dijalankan di database lokal. Perubahan pada migrasi yang sudah tercatat tidak akan berjalan ulang; untuk mengubah schema database yang sudah ada, gunakan migrasi baru dan pastikan schema database tujuan sesuai.

Demo account hanya ditanam oleh seeder pada environment `local` dan `testing`. Untuk membuat administrator pertama di database baru, jalankan perintah interaktif ini melalui terminal server setelah migrasi:

```sh
php artisan siuks:admin:create
```

Perintah tersebut meminta nama, email, dan password baru (minimal 12 karakter), lalu menolak jika akun admin sudah ada. Jangan memakai kredensial demo untuk produksi. Jika demo account pernah ditanam di server, ganti password atau hapus akun-akun tersebut sebelum aplikasi dibuka.

## Pemeriksaan sebelum dibuka

- Jalankan `composer audit`, `npm audit`, dan `php artisan test`.
- Pastikan login dan pendaftaran dapat diakses melalui HTTPS.
- Periksa log aplikasi dan pastikan `APP_DEBUG=false`.
- Pastikan database, sesi, cache, dan berkas yang diunggah tetap tersedia setelah proses deploy atau restart.
