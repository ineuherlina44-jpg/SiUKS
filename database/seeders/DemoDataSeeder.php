<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin Demo
        User::updateOrCreate(
            ['email' => 'admin@siuks.com'],
            [
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => 'admin123',
            ]
        );

        // 2. Petugas PMR Demo
        $pmrUser = User::updateOrCreate(
            ['email' => 'pmr@siuks.com'],
            [
                'name' => 'Siti Nurhaliza (Petugas PMR)',
                'role' => 'petugas_pmr',
                'password' => 'password123',
            ]
        );

        DB::table('petugas_pmr')->updateOrInsert(
            ['id_user' => $pmrUser->id],
            [
                'status_piket' => 'Bertugas',
                'nis' => 'PMR-001',
                'kelas' => 'RPL 1',
                'jenis_kelamin' => 'Perempuan',
                'status' => 'Aktif',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // 3. Wali Kelas Demo
        $waliUser = User::updateOrCreate(
            ['email' => 'wali@siuks.com'],
            [
                'name' => 'Budi Santoso, S.Pd. (Wali Kelas)',
                'role' => 'wali_kelas',
                'password' => 'password123',
            ]
        );

        DB::table('wali_kelas')->updateOrInsert(
            ['id_user' => $waliUser->id],
            [
                'kelas_dampu' => 'RPL 1',
                'nip' => '198501012010011001',
                'jurusan' => 'RPL',
                'status' => 'Aktif',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // 4. Siswa Demo
        $siswaUser = User::updateOrCreate(
            ['email' => 'siswa@siuks.com'],
            [
                'name' => 'Ineu Herlina (Siswa)',
                'role' => 'siswa',
                'password' => 'password123',
            ]
        );

        $siswaId = DB::table('siswa')->where('id_user', $siswaUser->id)->value('id');
        if (! $siswaId) {
            $siswaId = DB::table('siswa')->insertGetId([
                'id_user' => $siswaUser->id,
                'nisn' => '1234567890',
                'kelas' => 'RPL 1',
                'jenis_kelamin' => 'Perempuan',
                'riwayat_alergi' => 'Tidak ada',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('siswa')->where('id', $siswaId)->update([
                'nisn' => '1234567890',
                'kelas' => 'RPL 1',
                'jenis_kelamin' => 'Perempuan',
                'riwayat_alergi' => 'Tidak ada',
                'updated_at' => now(),
            ]);
        }

        // 5. Obat-obatan dasar
        $medicines = [
            ['kode_obat' => 'OBT-001', 'nama_obat' => 'Paracetamol 500mg', 'jenis' => 'Tablet', 'kategori' => 'Analgesik', 'stok_tersedia' => 30, 'satuan' => 'tablet'],
            ['kode_obat' => 'OBT-002', 'nama_obat' => 'Antasida Doen', 'jenis' => 'Tablet Kunyah', 'kategori' => 'Pencernaan', 'stok_tersedia' => 25, 'satuan' => 'tablet'],
            ['kode_obat' => 'OBT-003', 'nama_obat' => 'Betadine Antiseptik 30ml', 'jenis' => 'Cair', 'kategori' => 'Antiseptik', 'stok_tersedia' => 10, 'satuan' => 'botol'],
            ['kode_obat' => 'OBT-004', 'nama_obat' => 'Minyak Kayu Putih 60ml', 'jenis' => 'Cair', 'kategori' => 'Minyak Angin', 'stok_tersedia' => 15, 'satuan' => 'botol'],
            ['kode_obat' => 'OBT-005', 'nama_obat' => 'Oralit', 'jenis' => 'Serbuk', 'kategori' => 'Pencernaan', 'stok_tersedia' => 20, 'satuan' => 'sachet'],
        ];

        foreach ($medicines as $med) {
            DB::table('obat')->updateOrInsert(
                ['kode_obat' => $med['kode_obat']],
                array_merge($med, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 6. Contoh Riwayat Rekam Medis untuk Siswa
        $pmrId = DB::table('petugas_pmr')->where('id_user', $pmrUser->id)->value('id');
        $hasMedicalRecord = DB::table('rekam_medis')->where('id_siswa', $siswaId)->exists();
        if (! $hasMedicalRecord && $pmrId) {
            $recordId = DB::table('rekam_medis')->insertGetId([
                'id_siswa' => $siswaId,
                'id_petugas' => $pmrId,
                'waktu_pemeriksaan' => now()->subDay(),
                'keluhan' => 'Pusing dan sakit kepala ringan',
                'tindakan' => 'Istirahat di ruang UKS selama 30 menit dan minum air hangat',
                'status' => 'diizinkan_pulang',
                'status_penyakit' => 'Pusing',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]);

            $paracetamolId = DB::table('obat')->where('kode_obat', 'OBT-001')->value('id');
            if ($paracetamolId) {
                DB::table('detail_pemberian_obat')->insert([
                    'id_rekam_medis' => $recordId,
                    'id_obat' => $paracetamolId,
                    'jumlah_dosis' => 1,
                    'aturan_pakai' => '1 tablet setelah makan',
                    'created_at' => now()->subDay(),
                    'updated_at' => now()->subDay(),
                ]);
            }
        }
    }
}
