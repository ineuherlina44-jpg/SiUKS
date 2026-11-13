<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PmrRekamMedisTest extends TestCase
{
    use RefreshDatabase;

    public function test_pmr_can_search_and_filter_medical_history(): void
    {
        $staffUser = User::create([
            'name' => 'Petugas PMR',
            'email' => 'pmr@example.test',
            'role' => 'petugas_pmr',
            'password' => 'password123',
        ]);
        $staffId = DB::table('petugas_pmr')->insertGetId([
            'id_user' => $staffUser->id,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentUser = User::create([
            'name' => 'Siswa Riwayat',
            'email' => 'riwayat@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '9988776655',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('rekam_medis')->insert([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => '2026-10-08 09:30:00',
            'status_penyakit' => 'Demam',
            'keluhan' => 'Pusing dan suhu meningkat',
            'tindakan' => 'Istirahat di ruang UKS',
            'status' => 'diizinkan_pulang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('rekam_medis')->insert([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => '2026-10-09 09:30:00',
            'status_penyakit' => 'Batuk',
            'keluhan' => 'Batuk berkelanjutan',
            'tindakan' => 'Minum air hangat',
            'status' => 'diizinkan_pulang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($staffUser)
            ->get(route('pmr.rekam-medis.index', [
                'search' => 'Siswa Riwayat',
                'kelas' => 'XI IPA 1',
                'status' => 'diizinkan_pulang',
                'date_from' => '2026-10-08',
                'date_to' => '2026-10-08',
            ]))
            ->assertOk()
            ->assertSee('Riwayat Rekam Medis')
            ->assertSee('Siswa Riwayat')
            ->assertSee('Pusing dan suhu meningkat')
            ->assertSee('Istirahat di ruang UKS');

        $this->actingAs($staffUser)
            ->get(route('pmr.rekam-medis.index', ['date_to' => '2026-10-08']))
            ->assertOk()
            ->assertSee('Siswa Riwayat');

        $this->actingAs($staffUser)
            ->get(route('pmr.rekam-medis.index'))
            ->assertOk()
            ->assertSeeInOrder(['Pusing dan suhu meningkat', 'Batuk berkelanjutan']);
    }

    public function test_pmr_can_open_the_visit_form_and_save_a_student_visit(): void
    {
        $staffUser = User::create([
            'name' => 'Petugas PMR',
            'email' => 'pmr@example.test',
            'role' => 'petugas_pmr',
            'password' => 'password123',
        ]);
        $staffId = DB::table('petugas_pmr')->insertGetId([
            'id_user' => $staffUser->id,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentUser = User::create([
            'name' => 'Siswa Contoh',
            'email' => 'siswa@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '1234567890',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Perempuan',
            'riwayat_alergi' => 'Alergi udang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($staffUser)
            ->get(route('pmr.rekam-medis.create'))
            ->assertOk()
            ->assertSee('Catat Pasien Sakit')
            ->assertSee('Alergi udang')
            ->assertSee(route('pmr.rekam-medis.store'));

        $this->actingAs($staffUser)
            ->post(route('pmr.rekam-medis.store'), [
                'student_id' => $studentId,
                'waktu_pemeriksaan' => '2026-10-09T09:35',
                'status_penyakit_select' => 'Lainnya',
                'status_penyakit_custom' => 'Radang Amandel',
                'keluhan' => 'Pusing dan suhu badan meningkat',
                'tindakan' => 'Istirahat dan kompres',
                'status' => 'dirawat',
            ])
            ->assertRedirect(route('pmr.rekam-medis.create'))
            ->assertSessionHas('status', 'Catatan kunjungan berhasil disimpan.');

        $this->assertDatabaseHas('rekam_medis', [
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => '2026-10-09 09:35:00',
            'status_penyakit' => 'Radang Amandel',
            'keluhan' => 'Pusing dan suhu badan meningkat',
            'tindakan' => 'Istirahat dan kompres',
            'status' => 'dirawat',
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);
        $this->actingAs($admin)
            ->get(route('admin.rekam-medis.index'))
            ->assertOk()
            ->assertSee('Siswa Contoh')
            ->assertSee('Radang Amandel')
            ->assertSee('Pusing dan suhu badan meningkat');
    }

    public function test_non_pmr_users_cannot_open_the_visit_form(): void
    {
        $studentUser = User::create([
            'name' => 'Siswa Contoh',
            'email' => 'siswa@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);

        $this->actingAs($studentUser)
            ->get(route('pmr.rekam-medis.create'))
            ->assertForbidden();
    }

    public function test_medical_condition_photos_are_stored_on_private_disk(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $staffUser = User::create([
            'name' => 'Petugas PMR',
            'email' => 'pmr-photo@example.test',
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
            'name' => 'Siswa Foto',
            'email' => 'siswa-photo@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '9988776655',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($staffUser)
            ->post(route('rekam-medis.store'), [
                'id_siswa' => $studentId,
                'keluhan' => 'Memar ringan',
                'status_penyakit' => 'Memar',
                'tindakan' => 'Kompres dingin',
                'foto_kondisi' => UploadedFile::fake()->createWithContent(
                    'condition.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/tmcAAAAASUVORK5CYII=')
                ),
            ])
            ->assertRedirect(route('rekam-medis.index'));

        $fotoPath = DB::table('rekam_medis')->where('id_siswa', $studentId)->value('foto_kondisi');

        $this->assertNotEmpty($fotoPath);
        Storage::disk('local')->assertExists($fotoPath);
        Storage::disk('public')->assertMissing($fotoPath);
    }
}
