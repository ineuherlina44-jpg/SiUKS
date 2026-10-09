<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WaliKelasStudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_kelas_can_create_and_edit_students_in_their_class(): void
    {
        $teacher = $this->createTeacher('XI IPA 1');

        $this->actingAs($teacher)
            ->get(route('wali-kelas.siswa.index'))
            ->assertOk()
            ->assertSee('Data Siswa')
            ->assertSee('Tambah siswa');

        $this->actingAs($teacher)
            ->post(route('wali-kelas.siswa.store'), [
                'name' => 'Siswa Baru',
                'email' => 'siswa.baru@example.test',
                'password' => 'SiswaBaru123',
                'password_confirmation' => 'SiswaBaru123',
                'nisn' => '2000000001',
                'jenis_kelamin' => 'Perempuan',
                'riwayat_alergi' => 'Alergi kacang',
            ])
            ->assertRedirect(route('wali-kelas.siswa.index'))
            ->assertSessionHas('status', 'Data siswa dan akun login berhasil dibuat.');

        $studentUser = User::where('email', 'siswa.baru@example.test')->firstOrFail();
        $this->assertSame('siswa', $studentUser->role);
        $this->assertTrue(Hash::check('SiswaBaru123', $studentUser->password));
        $this->assertDatabaseHas('siswa', [
            'id_user' => $studentUser->id,
            'nisn' => '2000000001',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Perempuan',
            'riwayat_alergi' => 'Alergi kacang',
        ]);

        $studentId = DB::table('siswa')->where('id_user', $studentUser->id)->value('id');
        $this->actingAs($teacher)
            ->put(route('wali-kelas.siswa.update', $studentId), [
                'name' => 'Siswa Diperbarui',
                'nisn' => '2000000002',
                'jenis_kelamin' => 'Laki-laki',
                'riwayat_alergi' => null,
            ])
            ->assertRedirect(route('wali-kelas.siswa.index'))
            ->assertSessionHas('status', 'Data siswa berhasil diperbarui.');

        $this->assertDatabaseHas('users', ['id' => $studentUser->id, 'name' => 'Siswa Diperbarui']);
        $this->assertDatabaseHas('siswa', [
            'id' => $studentId,
            'nisn' => '2000000002',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'riwayat_alergi' => null,
        ]);
    }

    public function test_wali_kelas_cannot_manage_students_outside_their_class(): void
    {
        $teacher = $this->createTeacher('XI IPA 1');
        $studentUser = User::create([
            'name' => 'Siswa Kelas Lain',
            'email' => 'kelas.lain@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '2000000003',
            'kelas' => 'XI IPA 2',
            'jenis_kelamin' => 'Laki-laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->put(route('wali-kelas.siswa.update', $studentId), [
                'name' => 'Tidak Diizinkan',
                'nisn' => '2000000004',
                'jenis_kelamin' => 'Laki-laki',
            ])
            ->assertNotFound();

        $this->actingAs($teacher)
            ->delete(route('wali-kelas.siswa.destroy', $studentId))
            ->assertNotFound();

        $this->assertDatabaseHas('siswa', ['id' => $studentId, 'kelas' => 'XI IPA 2']);
    }

    public function test_student_with_health_history_cannot_be_deleted_but_student_without_history_can(): void
    {
        $teacher = $this->createTeacher('XI IPA 1');
        $staffUser = User::create([
            'name' => 'Petugas',
            'email' => 'petugas@example.test',
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
            'name' => 'Siswa dengan riwayat',
            'email' => 'riwayat@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '2000000005',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('rekam_medis')->insert([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => now(),
            'status' => 'dirawat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->delete(route('wali-kelas.siswa.destroy', $studentId))
            ->assertRedirect(route('wali-kelas.siswa.index'))
            ->assertSessionHas('error', 'Siswa tidak dapat dihapus karena sudah memiliki riwayat UKS atau laporan SOS.');

        $this->assertDatabaseHas('siswa', ['id' => $studentId]);
        $this->assertDatabaseHas('rekam_medis', ['id_siswa' => $studentId]);

        $cleanStudent = User::create([
            'name' => 'Siswa tanpa riwayat',
            'email' => 'tanpa.riwayat@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $cleanStudentId = DB::table('siswa')->insertGetId([
            'id_user' => $cleanStudent->id,
            'nisn' => '2000000006',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->delete(route('wali-kelas.siswa.destroy', $cleanStudentId))
            ->assertRedirect(route('wali-kelas.siswa.index'))
            ->assertSessionHas('status', 'Data siswa berhasil dihapus.');

        $this->assertDatabaseMissing('siswa', ['id' => $cleanStudentId]);
        $this->assertDatabaseMissing('users', ['id' => $cleanStudent->id]);
    }

    public function test_non_wali_kelas_users_cannot_create_student_accounts(): void
    {
        $student = User::create([
            'name' => 'Siswa',
            'email' => 'siswa@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);

        $this->actingAs($student)
            ->post(route('wali-kelas.siswa.store'), [
                'name' => 'Siswa Lain',
                'email' => 'lain@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'nisn' => '2000000007',
                'jenis_kelamin' => 'Laki-laki',
            ])
            ->assertForbidden();
    }

    private function createTeacher(string $class): User
    {
        $teacher = User::create([
            'name' => 'Wali Kelas',
            'email' => 'wali@example.test',
            'role' => 'wali_kelas',
            'password' => 'password123',
        ]);
        DB::table('wali_kelas')->insert([
            'id_user' => $teacher->id,
            'kelas_dampu' => $class,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $teacher;
    }
}
