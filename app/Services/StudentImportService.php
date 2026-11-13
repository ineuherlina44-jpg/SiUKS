<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class StudentImportService
{
    /**
     * Daftar kelas default yang tersedia untuk import otomatis
     */
    public static function getPresetClasses(): array
    {
        return [
            'RPL 1',
            'RPL 2',
            'RPL 3',
            'RPL 4',
            'DKV 1',
            'DKV 2',
            'DKV 3',
            'BR 1',
            'BR 2',
            'BR 3',
            'BR 4',
            'BR 5',
            'BR 6',
        ];
    }

    /**
     * Mengambil daftar data siswa untuk kelas tertentu
     */
    public static function getStudentsForClass(string $kelas): array
    {
        $presets = [
            'RPL 1' => [
                ['name' => 'Ineu Herlina', 'nisn' => '1234567890', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Ahmad Fauzan', 'nisn' => '0081234501', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Alifia Nur Ramadhani', 'nisn' => '0081234502', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
                ['name' => 'Bayu Pratama', 'nisn' => '0081234503', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Cantika Putri Maharani', 'nisn' => '0081234504', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Dimas Aditya Saputra', 'nisn' => '0081234505', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Paracetamol'],
                ['name' => 'Eka Safitri', 'nisn' => '0081234506', 'gender' => 'Perempuan', 'allergy' => 'Alergi Seafood'],
                ['name' => 'Fajar Hidayatullah', 'nisn' => '0081234507', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Gilang Ramadhan', 'nisn' => '0081234508', 'gender' => 'Laki-laki', 'allergy' => 'Riwayat Maag Kronis'],
                ['name' => 'Hana Aulia Zahra', 'nisn' => '0081234509', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Irfan Hakim Setiawan', 'nisn' => '0081234510', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Jessica Amanda Putri', 'nisn' => '0081234511', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
            ],
            'RPL 2' => [
                ['name' => 'Kevin Sanjaya', 'nisn' => '0081234512', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Larasati Dewi', 'nisn' => '0081234513', 'gender' => 'Perempuan', 'allergy' => 'Alergi Udara Dingin'],
                ['name' => 'Muhammad Rizky Pratama', 'nisn' => '0081234514', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Nabila Syakieb', 'nisn' => '0081234515', 'gender' => 'Perempuan', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Oktavianus Saputra', 'nisn' => '0081234516', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Putri Wulandari', 'nisn' => '0081234517', 'gender' => 'Perempuan', 'allergy' => 'Hipotensi Ringan'],
                ['name' => 'Raditya Dika Nugroho', 'nisn' => '0081234518', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Siti Rohmah Maryam', 'nisn' => '0081234519', 'gender' => 'Perempuan', 'allergy' => 'Asma'],
                ['name' => 'Taufik Hidayat', 'nisn' => '0081234520', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Vania Maharani', 'nisn' => '0081234521', 'gender' => 'Perempuan', 'allergy' => 'Alergi Antibiotik Penicilin'],
                ['name' => 'Wahyu Setiawan', 'nisn' => '0081234522', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Zahra Maulida', 'nisn' => '0081234523', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
            ],
            'RPL 3' => [
                ['name' => 'Aditia Nugraha', 'nisn' => '0081234524', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Bella Septiana', 'nisn' => '0081234525', 'gender' => 'Perempuan', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Candra Wijaya Kusuma', 'nisn' => '0081234526', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Dewi Lestari', 'nisn' => '0081234527', 'gender' => 'Perempuan', 'allergy' => 'Nyeri Sendi Ringan'],
                ['name' => 'Farhan Maulana', 'nisn' => '0081234528', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Gita Gutawa Putri', 'nisn' => '0081234529', 'gender' => 'Perempuan', 'allergy' => 'Alergi Udara Lembab'],
                ['name' => 'Hendra Kurniawan', 'nisn' => '0081234530', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Intan Permatasari', 'nisn' => '0081234531', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Joko Susilo', 'nisn' => '0081234532', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Ikan Tongkol'],
                ['name' => 'Kartika Sari Dewi', 'nisn' => '0081234533', 'gender' => 'Perempuan', 'allergy' => 'Dispepsia / Maag'],
                ['name' => 'Laila Rahmawati', 'nisn' => '0081234564', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Muhammad Farel', 'nisn' => '0081234565', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Kacang'],
            ],
            'RPL 4' => [
                ['name' => 'Lukman Hakim', 'nisn' => '0081234534', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Maya Anggraini', 'nisn' => '0081234535', 'gender' => 'Perempuan', 'allergy' => 'Alergi Telur'],
                ['name' => 'Naufal Azhar', 'nisn' => '0081234536', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Olivia Zalianty', 'nisn' => '0081234537', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Pandu Wicaksono', 'nisn' => '0081234538', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Qori Sandioriva', 'nisn' => '0081234539', 'gender' => 'Perempuan', 'allergy' => 'Alergi Cuaca Dingin'],
                ['name' => 'Rendy Pangalila', 'nisn' => '0081234540', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Salsabila Khairunnisa', 'nisn' => '0081234541', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
                ['name' => 'Tommy Kurniawan', 'nisn' => '0081234542', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Utari Ningrum', 'nisn' => '0081234543', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Viona Pramesti', 'nisn' => '0081234566', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Yoga Pratama', 'nisn' => '0081234567', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
            ],
            'DKV 1' => [
                ['name' => 'Aditya Putra', 'nisn' => '0081234568', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Anisa Lestari', 'nisn' => '0081234569', 'gender' => 'Perempuan', 'allergy' => 'Alergi Debu'],
                ['name' => 'Bimo Nugroho', 'nisn' => '0081234570', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Citra Maharani', 'nisn' => '0081234571', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Dimas Rahadian', 'nisn' => '0081234572', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Eka Putri', 'nisn' => '0081234573', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Fajar Rizky', 'nisn' => '0081234574', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Gadis Ayu', 'nisn' => '0081234575', 'gender' => 'Perempuan', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Hafiz Ardi', 'nisn' => '0081234576', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Ines Pratiwi', 'nisn' => '0081234577', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Johan Setiawan', 'nisn' => '0081234578', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Kirana Wulandari', 'nisn' => '0081234579', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
            ],
            'DKV 2' => [
                ['name' => 'Luthfi Ramadhan', 'nisn' => '0081234580', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Maulida Sari', 'nisn' => '0081234581', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Naufal Hidayat', 'nisn' => '0081234582', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Olivya Fani', 'nisn' => '0081234583', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Pandu Wibowo', 'nisn' => '0081234584', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Qina Aulia', 'nisn' => '0081234585', 'gender' => 'Perempuan', 'allergy' => 'Alergi Debu'],
                ['name' => 'Raka Prakoso', 'nisn' => '0081234586', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Salsa Dewi', 'nisn' => '0081234587', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Teguh Permana', 'nisn' => '0081234588', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Umi Kalsum', 'nisn' => '0081234589', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Vito Saputra', 'nisn' => '0081234590', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Winda Lestari', 'nisn' => '0081234591', 'gender' => 'Perempuan', 'allergy' => 'Alergi Udang'],
            ],
            'DKV 3' => [
                ['name' => 'Yusuf Maulana', 'nisn' => '0081234592', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Zahra Nadya', 'nisn' => '0081234593', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Ari Suryana', 'nisn' => '0081234594', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Bela Asri', 'nisn' => '0081234595', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Cahya Ramadhan', 'nisn' => '0081234596', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Dinda Novita', 'nisn' => '0081234597', 'gender' => 'Perempuan', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Edo Pratama', 'nisn' => '0081234598', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Fani Anggraini', 'nisn' => '0081234599', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Gilang Purnama', 'nisn' => '0081234600', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Hana Safira', 'nisn' => '0081234601', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Iqbal Firmansyah', 'nisn' => '0081234602', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Jihan Aulia', 'nisn' => '0081234603', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
            ],
            'BR 1' => [
                ['name' => 'Abdi Kurnia', 'nisn' => '0081234604', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Bunga Wati', 'nisn' => '0081234605', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Candra Wijaya', 'nisn' => '0081234606', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Dewi Lestari', 'nisn' => '0081234607', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Erwin Putra', 'nisn' => '0081234608', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Fadilah Sari', 'nisn' => '0081234609', 'gender' => 'Perempuan', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Guntur Pratama', 'nisn' => '0081234610', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Hana Kharisma', 'nisn' => '0081234611', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Indra Permana', 'nisn' => '0081234612', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Jasmine Putri', 'nisn' => '0081234613', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Kurniawan Hadi', 'nisn' => '0081234614', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Lina Suryani', 'nisn' => '0081234615', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
            ],
            'BR 2' => [
                ['name' => 'Miko Ramadhan', 'nisn' => '0081234616', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Nanda Sari', 'nisn' => '0081234617', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Omar Syahputra', 'nisn' => '0081234618', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Putri Anggraeni', 'nisn' => '0081234619', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Rafi Zulfikar', 'nisn' => '0081234620', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Siska Ramadhani', 'nisn' => '0081234621', 'gender' => 'Perempuan', 'allergy' => 'Alergi Udang'],
                ['name' => 'Taufik Hadi', 'nisn' => '0081234622', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Umi Choirunnisa', 'nisn' => '0081234623', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Vian Syaputra', 'nisn' => '0081234624', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Wulan Puspita', 'nisn' => '0081234625', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Yuda Wahyudi', 'nisn' => '0081234626', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Ziva Rahma', 'nisn' => '0081234627', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
            ],
            'BR 3' => [
                ['name' => 'Adit Yulianto', 'nisn' => '0081234628', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Berlian Sari', 'nisn' => '0081234629', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Cahyo Tri', 'nisn' => '0081234630', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Diah Ayu', 'nisn' => '0081234631', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Edo Saputra', 'nisn' => '0081234632', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Fira Nisa', 'nisn' => '0081234633', 'gender' => 'Perempuan', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Gian Pratama', 'nisn' => '0081234634', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Hafshah Putri', 'nisn' => '0081234635', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Idi Firmansyah', 'nisn' => '0081234636', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Jelita Sari', 'nisn' => '0081234637', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Kevin Darmawan', 'nisn' => '0081234638', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Lia Amanda', 'nisn' => '0081234639', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
            ],
            'BR 4' => [
                ['name' => 'M Rizky', 'nisn' => '0081234640', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Nabila Putri', 'nisn' => '0081234641', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Oky Prakoso', 'nisn' => '0081234642', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Puspita Wati', 'nisn' => '0081234643', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Qodri Ramadhan', 'nisn' => '0081234644', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Rinda Septiani', 'nisn' => '0081234645', 'gender' => 'Perempuan', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Satria Nugraha', 'nisn' => '0081234646', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Tiara Melati', 'nisn' => '0081234647', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Ulul Azmi', 'nisn' => '0081234648', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Vera Handayani', 'nisn' => '0081234649', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Wahyu Adi', 'nisn' => '0081234650', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Xena Priyanti', 'nisn' => '0081234651', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
            ],
            'BR 5' => [
                ['name' => 'Yandi Putra', 'nisn' => '0081234652', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Zulfa Rahma', 'nisn' => '0081234653', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Arif Nugroho', 'nisn' => '0081234654', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Bunga Ayu', 'nisn' => '0081234655', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Candra Putra', 'nisn' => '0081234656', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Diana Sari', 'nisn' => '0081234657', 'gender' => 'Perempuan', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Ega Purnama', 'nisn' => '0081234658', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Fika Rahmawati', 'nisn' => '0081234659', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Gilang Wijaya', 'nisn' => '0081234660', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Hilda Ayu', 'nisn' => '0081234661', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Iqbal Pratama', 'nisn' => '0081234662', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Jihan Zahra', 'nisn' => '0081234663', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
            ],
            'BR 6' => [
                ['name' => 'Kiki Samudra', 'nisn' => '0081234664', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Laila Indah', 'nisn' => '0081234665', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'M. Fadli', 'nisn' => '0081234666', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Debu'],
                ['name' => 'Nadia Puspita', 'nisn' => '0081234667', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Ozan Ramadhan', 'nisn' => '0081234668', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Pipit Maharani', 'nisn' => '0081234669', 'gender' => 'Perempuan', 'allergy' => 'Alergi Kacang'],
                ['name' => 'Rizki Ananda', 'nisn' => '0081234670', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Suci Ramadhani', 'nisn' => '0081234671', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Teguh Santoso', 'nisn' => '0081234672', 'gender' => 'Laki-laki', 'allergy' => 'Alergi Dingin'],
                ['name' => 'Vera Agustin', 'nisn' => '0081234673', 'gender' => 'Perempuan', 'allergy' => 'Tidak ada'],
                ['name' => 'Wira Pratama', 'nisn' => '0081234674', 'gender' => 'Laki-laki', 'allergy' => 'Tidak ada'],
                ['name' => 'Yuni Ariska', 'nisn' => '0081234675', 'gender' => 'Perempuan', 'allergy' => 'Asma Ringan'],
            ],
        ];

        if (isset($presets[$kelas])) {
            return $presets[$kelas];
        }

        // Generator otomatis untuk kelas custom
        return self::generateRosterForCustomClass($kelas);
    }

    /**
     * Generate daftar siswa acak realistis untuk kelas custom
     */
    protected static function generateRosterForCustomClass(string $kelas): array
    {
        $firstNamesL = ['Aris', 'Bambang', 'Cahyo', 'Dharma', 'Eko', 'Fandi', 'Guntur', 'Hadi', 'Ilyas', 'Joko'];
        $firstNamesP = ['Annisa', 'Bunga', 'Citra', 'Dina', 'Erna', 'Febby', 'Gita', 'Hesti', 'Indah', 'Jelita'];
        $lastNames = ['Pratama', 'Saputra', 'Kusuma', 'Santoso', 'Wijaya', 'Hidayat', 'Wulandari', 'Utami', 'Lestari', 'Anggraini'];
        $allergies = ['Tidak ada', 'Tidak ada', 'Tidak ada', 'Alergi Dingin', 'Alergi Debu', 'Asma Ringan', 'Alergi Makanan Laut'];

        $roster = [];
        $cleanClassCode = preg_replace('/[^0-9]/', '', $kelas);
        if (empty($cleanClassCode)) {
            $cleanClassCode = abs(crc32($kelas)) % 900 + 100;
        }

        for ($i = 1; $i <= 10; $i++) {
            $isMale = ($i % 2 === 1);
            $firstName = $isMale ? $firstNamesL[($i - 1) % count($firstNamesL)] : $firstNamesP[($i - 1) % count($firstNamesP)];
            $lastName = $lastNames[($i + 2) % count($lastNames)];
            $name = "$firstName $lastName";
            $nisn = sprintf('008%03d%04d', (int) $cleanClassCode, $i);

            $roster[] = [
                'name' => $name,
                'nisn' => $nisn,
                'gender' => $isMale ? 'Laki-laki' : 'Perempuan',
                'allergy' => $allergies[$i % count($allergies)],
            ];
        }

        return $roster;
    }

    /**
     * Mengimpor seluruh siswa berdasarkan kelas
     */
    public static function importRosterForClass(string $kelas): array
    {
        if (! in_array(config('app.env'), ['local', 'testing'], true)) {
            throw new RuntimeException('Roster contoh hanya tersedia di local/testing. Gunakan berkas CSV data sekolah.');
        }

        $students = self::getStudentsForClass($kelas);
        $importedCount = 0;
        $updatedCount = 0;
        $credentials = [];

        DB::transaction(function () use ($students, $kelas, &$importedCount, &$updatedCount, &$credentials) {
            foreach ($students as $data) {
                $email = Str::slug($data['name'], '.') . '.' . substr($data['nisn'], -4) . '@siswa.siuks.com';

                // Cek apakah siswa dengan NISN ini sudah terdaftar
                $existingSiswa = DB::table('siswa')->where('nisn', $data['nisn'])->first();

                if ($existingSiswa) {
                    // Update data kelas dan alergi jika sudah ada
                    DB::table('siswa')->where('id', $existingSiswa->id)->update([
                        'kelas' => $kelas,
                        'jenis_kelamin' => $data['gender'],
                        'riwayat_alergi' => $data['allergy'],
                        'updated_at' => now(),
                    ]);
                    $updatedCount++;
                    continue;
                }

                $account = self::createImportedStudentAccount($data['name'], $email);
                $user = $account['user'];

                // Insert ke tabel siswa
                DB::table('siswa')->insert([
                    'id_user' => $user->id,
                    'nisn' => $data['nisn'],
                    'kelas' => $kelas,
                    'jenis_kelamin' => $data['gender'],
                    'riwayat_alergi' => $data['allergy'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $credentials[] = [
                    'name' => $data['name'],
                    'nisn' => $data['nisn'],
                    'password' => $account['password'],
                ];
                $importedCount++;
            }
        });

        return [
            'total' => count($students),
            'imported' => $importedCount,
            'updated' => $updatedCount,
            'class' => $kelas,
            'credentials' => $credentials,
        ];
    }

    /**
     * Mengimpor data siswa dari file CSV yang diunggah
     */
    public static function importFromCsv(UploadedFile $file, ?string $fallbackClass = null): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            throw new \Exception('Gagal membuka berkas CSV yang diunggah.');
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            fclose($handle);
            throw new \Exception('Berkas CSV kosong atau format tidak sesuai.');
        }

        // Normalisasi header
        $headerMap = [];
        foreach ($header as $index => $col) {
            $cleanCol = strtolower(trim($col));
            $headerMap[$cleanCol] = $index;
        }

        $importedCount = 0;
        $updatedCount = 0;
        $credentials = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $name = $row[$headerMap['nama'] ?? $headerMap['name'] ?? 0] ?? null;
                $nisn = $row[$headerMap['nisn'] ?? 1] ?? null;
                $kelas = $row[$headerMap['kelas'] ?? $headerMap['class'] ?? 2] ?? $fallbackClass;
                $gender = $row[$headerMap['jenis_kelamin'] ?? $headerMap['gender'] ?? 3] ?? 'Laki-laki';
                $allergy = $row[$headerMap['riwayat_alergi'] ?? $headerMap['allergy'] ?? 4] ?? 'Tidak ada';

                if (empty($name) || empty($nisn) || empty($kelas)) {
                    continue;
                }

                $email = Str::slug($name, '.') . '.' . substr($nisn, -4) . '@siswa.siuks.com';

                $existingSiswa = DB::table('siswa')->where('nisn', $nisn)->first();
                if ($existingSiswa) {
                    DB::table('siswa')->where('id', $existingSiswa->id)->update([
                        'kelas' => $kelas,
                        'jenis_kelamin' => $gender,
                        'riwayat_alergi' => $allergy,
                        'updated_at' => now(),
                    ]);
                    $updatedCount++;
                    continue;
                }

                $account = self::createImportedStudentAccount($name, $email);
                $user = $account['user'];

                DB::table('siswa')->insert([
                    'id_user' => $user->id,
                    'nisn' => $nisn,
                    'kelas' => $kelas,
                    'jenis_kelamin' => $gender,
                    'riwayat_alergi' => $allergy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $credentials[] = [
                    'name' => $name,
                    'nisn' => $nisn,
                    'password' => $account['password'],
                ];
                $importedCount++;
            }

            DB::commit();
            fclose($handle);

            return [
                'imported' => $importedCount,
                'updated' => $updatedCount,
                'credentials' => $credentials,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }
    }

    private static function createImportedStudentAccount(string $name, string $email): array
    {
        $temporaryPassword = Str::random(24);
        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->role !== 'siswa') {
                throw new RuntimeException('Email akun siswa bertabrakan dengan akun non-siswa.');
            }

            $user->forceFill([
                'name' => $name,
                'password' => $temporaryPassword,
                'password_change_required' => true,
            ])->save();
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'role' => 'siswa',
                'password' => $temporaryPassword,
                'password_change_required' => true,
            ]);
        }

        return [
            'user' => $user,
            'password' => $temporaryPassword,
        ];
    }

    /**
     * Generate template CSV contoh untuk diunduh
     */
    public static function generateSampleCsv(string $kelas = 'RPL 1'): string
    {
        $output = "nisn,nama,kelas,jenis_kelamin,riwayat_alergi\n";
        $students = self::getStudentsForClass($kelas);

        foreach ($students as $s) {
            $output .= "{$s['nisn']},\"{$s['name']}\",{$kelas},{$s['gender']},\"{$s['allergy']}\"\n";
        }

        return $output;
    }
}
