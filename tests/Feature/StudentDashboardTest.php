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
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('https://wa.me/6281234567890?text=', false)
            ->assertSee('Hubungi orang tua via WhatsApp')
            ->assertSee('Pesan tidak dikirim otomatis.');

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

    public function test_sos_remains_available_when_parent_whatsapp_number_is_missing(): void
    {
        [$student, $studentId] = $this->createStudent();
        DB::table('siswa')->where('id', $studentId)->update(['nomor_whatsapp_orang_tua' => null]);

        $this->actingAs($student)
            ->post(route('siswa.sos.store'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Nomor WhatsApp orang tua/wali belum tercatat.')
            ->assertDontSee('Hubungi orang tua via WhatsApp');

        $this->assertDatabaseHas('sinyal_darurat', [
            'id_siswa' => $studentId,
            'status_respon' => 'menunggu',
        ]);
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
            'nomor_whatsapp_orang_tua' => '0812-3456-7890',
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
