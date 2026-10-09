<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PmrLaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_pmr_can_open_reports_page_and_download_each_pdf_report(): void
    {
        $staffUser = User::create([
            'name' => 'Petugas PMR',
            'email' => 'pmr@example.test',
            'role' => 'petugas_pmr',
            'password' => 'password123',
        ]);
        DB::table('petugas_pmr')->insert([
            'id_user' => $staffUser->id,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentUser = User::create([
            'name' => 'Siswa Laporan',
            'email' => 'siswa@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '1122334455',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $staffId = DB::table('petugas_pmr')->where('id_user', $staffUser->id)->value('id');
        DB::table('rekam_medis')->insert([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => '2026-10-08 09:30:00',
            'status_penyakit' => 'Demam',
            'keluhan' => 'Pusing',
            'tindakan' => 'Istirahat',
            'status' => 'diizinkan_pulang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('obat')->insert([
            'kode_obat' => 'OBT-001',
            'nama_obat' => 'Paracetamol',
            'jenis' => 'Tablet',
            'kategori' => 'Pereda nyeri',
            'stok_tersedia' => 4,
            'satuan' => 'tablet',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($staffUser)
            ->get(route('pmr.laporan.index'))
            ->assertOk()
            ->assertSee('Laporan Pasien')
            ->assertSee('Laporan Stok Obat')
            ->assertSee('Laporan Rekam Medis')
            ->assertSee('Laporan Kunjungan');

        foreach (['patients', 'stock', 'medical', 'visits'] as $type) {
            $response = $this->actingAs($staffUser)->get(route('pmr.laporan.download', [
                'type' => $type,
                'date_from' => '2026-10-01',
                'date_to' => '2026-10-31',
            ]));

            $response->assertOk()
                ->assertHeader('Content-Type', 'application/pdf')
                ->assertHeader('Content-Disposition');
            $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
            $this->assertStringContainsString('%%EOF', $response->getContent());
        }
    }

    public function test_report_download_rejects_invalid_period_and_non_pmr_users(): void
    {
        $staffUser = User::create([
            'name' => 'Petugas PMR',
            'email' => 'pmr@example.test',
            'role' => 'petugas_pmr',
            'password' => 'password123',
        ]);

        $this->actingAs($staffUser)
            ->get(route('pmr.laporan.download', [
                'type' => 'medical',
                'date_from' => '2026-10-10',
                'date_to' => '2026-10-01',
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('date_to');

        $studentUser = User::create([
            'name' => 'Siswa Contoh',
            'email' => 'student@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);

        $this->actingAs($studentUser)
            ->get(route('pmr.laporan.index'))
            ->assertForbidden();
        $this->actingAs($studentUser)
            ->get(route('pmr.laporan.download', 'medical'))
            ->assertForbidden();
    }
}
