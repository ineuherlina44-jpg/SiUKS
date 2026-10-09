<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_send_one_open_sos_alert(): void
    {
        [$student, $studentId] = $this->createStudent();

        $this->actingAs($student)
            ->post(route('siswa.sos.store'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Sinyal SOS berhasil dikirim dan tercatat untuk petugas UKS.');

        $this->assertDatabaseHas('sinyal_darurat', [
            'id_siswa' => $studentId,
            'id_pelapor' => $student->id,
            'status_respon' => 'menunggu',
        ]);

        $this->actingAs($student)
            ->post(route('siswa.sos.store'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Sinyal SOS Anda masih menunggu penanganan. Petugas UKS sudah menerima laporan tersebut.');

        $this->assertSame(1, DB::table('sinyal_darurat')->where('id_siswa', $studentId)->count());

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('SOS menunggu petugas')
            ->assertDontSee('Kirim SOS');
    }

    public function test_only_students_can_send_sos(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');

        $this->actingAs($admin)
            ->post(route('siswa.sos.store'))
            ->assertForbidden();
    }

    public function test_student_navigation_is_available_across_all_student_pages(): void
    {
        [$student] = $this->createStudent();
        $this->actingAs($student);

        foreach ([
            route('dashboard'),
            route('siswa.riwayat-kesehatan'),
            route('siswa.stok-obat'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('data-student-sidebar-toggle', false)
                ->assertSee(route('siswa.riwayat-kesehatan'), false)
                ->assertSee(route('siswa.stok-obat'), false);
        }
    }

    private function createStudent(): array
    {
        $student = $this->createUser('Siswa', 'siswa@example.test', 'siswa');
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $student->id,
            'nisn' => '3000000001',
            'kelas' => 'RPL 4',
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$student, $studentId];
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
}
