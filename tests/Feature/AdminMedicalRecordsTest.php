<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminMedicalRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_and_filter_medical_records_and_view_medicine_details(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        $staffId = $this->createStaff();
        $studentId = $this->createStudent('Siswa Rekam', '4000000001', 'XI IPA 1');
        $otherStudentId = $this->createStudent('Siswa Lain', '4000000002', 'X IPA 2');
        $recordId = $this->createRecord($studentId, $staffId, '2026-10-08 09:30:00', 'Demam', 'Pusing', 'diizinkan_pulang');
        $this->createRecord($otherStudentId, $staffId, '2026-09-30 09:30:00', 'Batuk', 'Tenggorokan sakit', 'dirawat');
        $medicineId = DB::table('obat')->insertGetId([
            'kode_obat' => 'OBT-400',
            'nama_obat' => 'Paracetamol',
            'jenis' => 'Tablet',
            'kategori' => 'Analgesik',
            'stok_tersedia' => 20,
            'satuan' => 'tablet',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('detail_pemberian_obat')->insert([
            'id_rekam_medis' => $recordId,
            'id_obat' => $medicineId,
            'jumlah_dosis' => 2,
            'aturan_pakai' => 'Sesudah makan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.rekam-medis.index', [
                'search' => 'Siswa Rekam',
                'type' => 'Demam',
                'status' => 'diizinkan_pulang',
                'date_from' => '2026-10-01',
                'date_to' => '2026-10-31',
            ]))
            ->assertOk()
            ->assertSee('Riwayat Rekam Medis')
            ->assertSee('Siswa Rekam')
            ->assertSee('Paracetamol')
            ->assertSee('Sesudah makan')
            ->assertDontSee('Siswa Lain');
    }

    public function test_admin_can_download_pdf_matching_selected_medical_record_filters(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        $staffId = $this->createStaff();
        $studentId = $this->createStudent('Siswa PDF', '4000000003', 'XI IPA 1');
        $this->createRecord($studentId, $staffId, '2026-10-08 09:30:00', 'Cedera', 'Lutut terkilir', 'rujukan');

        $response = $this->actingAs($admin)
            ->get(route('admin.rekam-medis.download', [
                'search' => 'Siswa PDF',
                'type' => 'Cedera',
                'status' => 'rujukan',
                'date_from' => '2026-10-01',
                'date_to' => '2026-10-31',
            ]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition');

        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
        $this->assertStringContainsString('Siswa PDF', $response->getContent());
        $this->assertStringContainsString('Lutut terkilir', $response->getContent());
    }

    public function test_non_admin_cannot_view_or_export_medical_records(): void
    {
        $user = $this->createUser('Petugas PMR', 'pmr@example.test', 'petugas_pmr');

        $this->actingAs($user)
            ->get(route('admin.rekam-medis.index'))
            ->assertForbidden();
        $this->actingAs($user)
            ->get(route('admin.rekam-medis.download'))
            ->assertForbidden();
    }

    private function createUser(string $name, string $email, string $role): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => 'password123',
        ]);
    }

    private function createStudent(string $name, string $nisn, string $class): int
    {
        $user = $this->createUser($name, $nisn.'@example.test', 'siswa');

        return DB::table('siswa')->insertGetId([
            'id_user' => $user->id,
            'nisn' => $nisn,
            'kelas' => $class,
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createStaff(): int
    {
        $user = $this->createUser('Petugas PMR', 'staff@example.test', 'petugas_pmr');

        return DB::table('petugas_pmr')->insertGetId([
            'id_user' => $user->id,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createRecord(int $studentId, int $staffId, string $date, string $diagnosis, string $complaint, string $status): int
    {
        return DB::table('rekam_medis')->insertGetId([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => $date,
            'status_penyakit' => $diagnosis,
            'keluhan' => $complaint,
            'tindakan' => 'Istirahat',
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
