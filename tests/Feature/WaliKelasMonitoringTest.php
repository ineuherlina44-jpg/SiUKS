<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WaliKelasMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_kelas_can_filter_monitoring_to_students_in_their_class(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 9)->setTime(12, 0));
        $teacher = User::create([
            'name' => 'Wali Kelas XI',
            'email' => 'wali@example.test',
            'role' => 'wali_kelas',
            'password' => 'password123',
        ]);
        DB::table('wali_kelas')->insert([
            'id_user' => $teacher->id,
            'kelas_dampu' => 'XI IPA 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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

        $sickStudent = $this->createStudent('Siswa Sakit', '1000000001', 'XI IPA 1');
        $this->createStudent('Siswa Sehat', '1000000002', 'XI IPA 1');
        $otherClassStudent = $this->createStudent('Siswa Kelas Lain', '1000000003', 'XI IPA 2');
        DB::table('rekam_medis')->insert([
            'id_siswa' => $sickStudent,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => '2026-10-08 09:30:00',
            'status_penyakit' => 'Demam',
            'keluhan' => 'Pusing',
            'tindakan' => 'Istirahat',
            'status' => 'diizinkan_pulang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sinyal_darurat')->insert([
            'id_siswa' => $sickStudent,
            'id_pelapor' => $teacher->id,
            'waktu_pemicu' => '2026-10-08 10:00:00',
            'status_respon' => 'ditangani',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->get(route('wali-kelas.monitoring', [
                'month' => '2026-10',
                'status' => 'sakit',
                'search' => 'Siswa',
            ]))
            ->assertOk()
            ->assertSee('Monitoring Siswa')
            ->assertSee('Siswa Sakit')
            ->assertDontSee('Siswa Sehat')
            ->assertDontSee('Siswa Kelas Lain')
            ->assertViewHas('totalStudents', 2)
            ->assertViewHas('sickStudents', 1)
            ->assertViewHas('visitedStudents', 1)
            ->assertViewHas('sosThisMonth', 1);

        $this->actingAs($teacher)
            ->get(route('wali-kelas.monitoring', ['month' => '2026-10', 'status' => 'sehat']))
            ->assertOk()
            ->assertSee('Siswa Sehat')
            ->assertDontSee('Siswa Sakit')
            ->assertDontSee('Siswa Kelas Lain');

        $this->actingAs($teacher)
            ->get(route('wali-kelas.monitoring', ['student' => $sickStudent]))
            ->assertOk()
            ->assertSee('Detail kesehatan siswa')
            ->assertSee('Siswa Sakit')
            ->assertSee('Demam')
            ->assertSee('Pusing');

        $this->actingAs($teacher)
            ->get(route('wali-kelas.monitoring', ['student' => $otherClassStudent]))
            ->assertNotFound();
    }

    public function test_non_wali_kelas_users_cannot_access_student_monitoring(): void
    {
        $student = User::create([
            'name' => 'Siswa',
            'email' => 'siswa@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);

        $this->actingAs($student)
            ->get(route('wali-kelas.monitoring'))
            ->assertForbidden();
    }

    private function createStudent(string $name, string $nisn, string $class): int
    {
        $user = User::create([
            'name' => $name,
            'email' => $nisn . '@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);

        return DB::table('siswa')->insertGetId([
            'id_user' => $user->id,
            'nisn' => $nisn,
            'kelas' => $class,
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
