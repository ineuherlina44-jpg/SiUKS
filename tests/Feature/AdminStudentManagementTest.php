<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_filter_and_view_students_from_all_classes(): void
    {
        $admin = $this->createAdmin();
        $sickStudent = $this->createStudent('Siswa Sakit', '3000000001', 'XI IPA 1');
        $this->createStudent('Siswa Sehat', '3000000002', 'XI IPA 2');
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
        DB::table('rekam_medis')->insert([
            'id_siswa' => $sickStudent,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => now(),
            'status' => 'dirawat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.siswa.index'))
            ->assertOk()
            ->assertSee('Data Siswa')
            ->assertSee('Siswa Sakit')
            ->assertSee('Siswa Sehat')
            ->assertSee('XI IPA 1')
            ->assertSee('XI IPA 2')
            ->assertViewHas('totalStudents', 2)
            ->assertViewHas('sickStudents', 1);

        $this->actingAs($admin)
            ->get(route('admin.siswa.index', ['kelas' => 'XI IPA 1', 'status' => 'sakit']))
            ->assertOk()
            ->assertSee('Siswa Sakit')
            ->assertDontSee('Siswa Sehat');
    }

    public function test_admin_can_create_and_edit_student_accounts_and_details(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.siswa.store'), [
                'name' => 'Siswa Baru',
                'email' => 'siswa.baru@example.test',
                'password' => 'SiswaBaru123',
                'password_confirmation' => 'SiswaBaru123',
                'nisn' => '3000000003',
                'kelas' => 'X IPA 3',
                'jenis_kelamin' => 'Perempuan',
                'nomor_whatsapp_orang_tua' => '081234567891',
                'riwayat_alergi' => 'Alergi susu',
            ])
            ->assertRedirect(route('admin.siswa.index'))
            ->assertSessionHas('status', 'Data siswa dan akun login berhasil dibuat.');

        $studentUser = User::where('email', 'siswa.baru@example.test')->firstOrFail();
        $this->assertSame('siswa', $studentUser->role);
        $this->assertTrue(Hash::check('SiswaBaru123', $studentUser->password));
        $studentId = DB::table('siswa')->where('id_user', $studentUser->id)->value('id');
        $this->assertDatabaseHas('siswa', [
            'id' => $studentId,
            'kelas' => 'X IPA 3',
            'nomor_whatsapp_orang_tua' => '081234567891',
            'riwayat_alergi' => 'Alergi susu',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.siswa.update', $studentId), [
                'name' => 'Siswa Diperbarui',
                'email' => 'siswa.updated@example.test',
                'nisn' => '3000000004',
                'kelas' => 'XI IPA 1',
                'jenis_kelamin' => 'Laki-laki',
                'nomor_whatsapp_orang_tua' => '081234567892',
                'riwayat_alergi' => null,
            ])
            ->assertRedirect(route('admin.siswa.index'))
            ->assertSessionHas('status', 'Data siswa berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id' => $studentUser->id,
            'name' => 'Siswa Diperbarui',
            'email' => 'siswa.updated@example.test',
        ]);
        $this->assertDatabaseHas('siswa', [
            'id' => $studentId,
            'nisn' => '3000000004',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'nomor_whatsapp_orang_tua' => '081234567892',
            'riwayat_alergi' => null,
        ]);
    }

    public function test_admin_gets_clear_warnings_for_duplicate_student_email_and_nisn(): void
    {
        $admin = $this->createAdmin();
        $this->createStudent('Siswa Terdaftar', '3000000010', 'X IPA 1');
        User::create([
            'name' => 'Akun lain',
            'email' => 'terpakai@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.siswa.store'), [
                'name' => 'Siswa Duplikat',
                'email' => 'terpakai@example.test',
                'password' => 'SiswaBaru123',
                'password_confirmation' => 'SiswaBaru123',
                'nisn' => '3000000010',
                'kelas' => 'X IPA 1',
                'jenis_kelamin' => 'Laki-laki',
                'nomor_whatsapp_orang_tua' => '081234567893',
            ])
            ->assertSessionHasErrors([
                'email' => 'Email ini sudah terdaftar.',
                'nisn' => 'NISN ini sudah terdaftar.',
            ]);
    }

    public function test_admin_cannot_delete_students_with_health_history_but_can_delete_students_without_it(): void
    {
        $admin = $this->createAdmin();
        $studentId = $this->createStudent('Siswa Dengan Riwayat', '3000000005', 'X IPA 1');
        $studentUserId = DB::table('siswa')->where('id', $studentId)->value('id_user');
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
        DB::table('rekam_medis')->insert([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => now(),
            'status' => 'dirawat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.siswa.destroy', $studentId))
            ->assertRedirect(route('admin.siswa.index'))
            ->assertSessionHas('error', 'Siswa tidak dapat dihapus karena sudah memiliki riwayat UKS atau laporan SOS.');

        $this->assertDatabaseHas('siswa', ['id' => $studentId]);
        $this->assertDatabaseHas('rekam_medis', ['id_siswa' => $studentId]);

        $cleanStudentId = $this->createStudent('Siswa Tanpa Riwayat', '3000000006', 'X IPA 1');
        $cleanStudentUserId = DB::table('siswa')->where('id', $cleanStudentId)->value('id_user');
        $this->actingAs($admin)
            ->delete(route('admin.siswa.destroy', $cleanStudentId))
            ->assertRedirect(route('admin.siswa.index'))
            ->assertSessionHas('status', 'Data siswa berhasil dihapus.');

        $this->assertDatabaseMissing('siswa', ['id' => $cleanStudentId]);
        $this->assertDatabaseMissing('users', ['id' => $cleanStudentUserId]);
        $this->assertDatabaseHas('users', ['id' => $studentUserId]);
    }

    public function test_non_admin_cannot_access_or_mutate_admin_student_data(): void
    {
        $student = User::create([
            'name' => 'Siswa',
            'email' => 'siswa@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);

        $this->actingAs($student)
            ->get(route('admin.siswa.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->post(route('admin.siswa.store'), [
                'name' => 'Siswa Lain',
                'email' => 'siswa.lain@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'nisn' => '3000000007',
                'kelas' => 'X IPA 1',
                'jenis_kelamin' => 'Laki-laki',
            ])
            ->assertForbidden();
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);
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
