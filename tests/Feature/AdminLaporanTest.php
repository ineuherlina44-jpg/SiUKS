<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminLaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_report_shows_period_metrics_monthly_summary_and_visit_details(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        [$studentId, $otherStudentId] = $this->createStudents();
        $staffId = $this->createStaff();
        $from = now()->startOfMonth()->toDateString();
        $to = now()->toDateString();
        $firstVisit = now()->subDays(2);
        $secondVisit = now()->subDay();

        $this->createVisit($studentId, $staffId, $firstVisit->toDateTimeString(), 'Demam', 'Badan panas');
        $this->createVisit($studentId, $staffId, $secondVisit->toDateTimeString(), 'Demam', 'Pusing');
        $this->createVisit($otherStudentId, $staffId, now()->subMonths(2)->toDateTimeString(), 'Batuk', 'Batuk');

        $this->actingAs($admin)
            ->get(route('admin.laporan.index', ['date_from' => $from, 'date_to' => $to]))
            ->assertOk()
            ->assertSee('Laporan Kunjungan UKS')
            ->assertSee('Siswa Laporan')
            ->assertDontSee('Siswa Di Luar Periode')
            ->assertViewHas('totalVisits', 2)
            ->assertViewHas('uniqueStudents', 1)
            ->assertViewHas('complaintSlices', fn ($slices) => $slices->first()['name'] === 'Demam' && $slices->first()['percentage'] === 100)
            ->assertViewHas('monthlyChart', fn ($chart) => $chart->sum('visits') === 3 && $chart->sum('students') === 2);
    }

    public function test_admin_can_download_pdf_for_selected_period_and_invalid_dates_are_rejected(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        [$studentId] = $this->createStudents();
        $staffId = $this->createStaff();
        $date = now()->subDay();
        $this->createVisit($studentId, $staffId, $date->toDateTimeString(), 'Demam', 'Badan panas');

        $response = $this->actingAs($admin)
            ->get(route('admin.laporan.download', [
                'date_from' => $date->toDateString(),
                'date_to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
        $this->assertStringContainsString('Siswa Laporan', $response->getContent());

        $this->actingAs($admin)
            ->get(route('admin.laporan.index', [
                'date_from' => now()->toDateString(),
                'date_to' => now()->subDay()->toDateString(),
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('date_to');
    }

    public function test_non_admin_users_cannot_access_admin_reports(): void
    {
        $wali = $this->createUser('Wali Kelas', 'wali@example.test', 'wali_kelas');

        $this->actingAs($wali)
            ->get(route('admin.laporan.index'))
            ->assertForbidden();

        $this->actingAs($wali)
            ->get(route('admin.laporan.download'))
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

    private function createStudents(): array
    {
        $studentId = $this->createStudent('Siswa Laporan', '1000000001');
        $otherStudentId = $this->createStudent('Siswa Di Luar Periode', '1000000002');

        return [$studentId, $otherStudentId];
    }

    private function createStudent(string $name, string $nisn): int
    {
        $user = $this->createUser($name, $nisn.'@example.test', 'siswa');

        return DB::table('siswa')->insertGetId([
            'id_user' => $user->id,
            'nisn' => $nisn,
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createStaff(): int
    {
        $user = $this->createUser('Petugas PMR', 'pmr@example.test', 'petugas_pmr');

        return DB::table('petugas_pmr')->insertGetId([
            'id_user' => $user->id,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createVisit(int $studentId, int $staffId, string $date, string $diagnosis, string $complaint): void
    {
        DB::table('rekam_medis')->insert([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => $date,
            'status_penyakit' => $diagnosis,
            'keluhan' => $complaint,
            'tindakan' => 'Istirahat',
            'status' => 'diizinkan_pulang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
