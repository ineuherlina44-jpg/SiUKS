<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminSosMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_sos_alerts_by_period_and_status(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        $studentId = $this->createStudent('Siswa SOS', '3000000001', 'XI IPA 1');
        $otherStudentId = $this->createStudent('Siswa Lain', '3000000002', 'X IPA 2');
        $this->createAlert($studentId, $admin->id, '2026-10-08 10:00:00', 'menunggu');
        $this->createAlert($otherStudentId, $admin->id, '2026-09-30 10:00:00', 'ditangani');

        $this->actingAs($admin)
            ->get(route('admin.sos.index', [
                'date_from' => '2026-10-01',
                'date_to' => '2026-10-31',
                'status' => 'menunggu',
            ]))
            ->assertOk()
            ->assertSee('Monitoring Sinyal SOS')
            ->assertSee('Siswa SOS')
            ->assertSee('Menunggu')
            ->assertDontSee('Siswa Lain')
            ->assertViewHas('totalAlerts', 1)
            ->assertViewHas('waitingAlerts', 1);
    }

    public function test_admin_can_update_sos_status_and_keep_selected_period(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        $studentId = $this->createStudent('Siswa SOS', '3000000003', 'XI IPA 1');
        $alertId = $this->createAlert($studentId, $admin->id, '2026-10-08 10:00:00', 'menunggu');

        $this->actingAs($admin)
            ->patch(route('admin.sos.status', $alertId), [
                'status_respon' => 'selesai',
                'date_from' => '2026-10-01',
                'date_to' => '2026-10-31',
                'status' => 'all',
            ])
            ->assertRedirect(route('admin.sos.index', [
                'date_from' => '2026-10-01',
                'date_to' => '2026-10-31',
                'status' => 'all',
            ]))
            ->assertSessionHas('status', 'Status sinyal SOS berhasil diperbarui.');

        $this->assertDatabaseHas('sinyal_darurat', [
            'id' => $alertId,
            'status_respon' => 'selesai',
        ]);
    }

    public function test_non_admin_cannot_view_or_update_sos_monitoring(): void
    {
        $studentUser = $this->createUser('Siswa', 'siswa@example.test', 'siswa');
        $studentId = $this->createStudent('Siswa', '3000000004', 'XI IPA 1');
        $alertId = $this->createAlert($studentId, $studentUser->id, '2026-10-08 10:00:00', 'menunggu');

        $this->actingAs($studentUser)
            ->get(route('admin.sos.index'))
            ->assertForbidden();
        $this->actingAs($studentUser)
            ->patch(route('admin.sos.status', $alertId), ['status_respon' => 'selesai'])
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
        $student = $this->createUser($name, $nisn.'@example.test', 'siswa');

        return DB::table('siswa')->insertGetId([
            'id_user' => $student->id,
            'nisn' => $nisn,
            'kelas' => $class,
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createAlert(int $studentId, int $reporterId, string $triggeredAt, string $status): int
    {
        return DB::table('sinyal_darurat')->insertGetId([
            'id_siswa' => $studentId,
            'id_pelapor' => $reporterId,
            'waktu_pemicu' => $triggeredAt,
            'status_respon' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
