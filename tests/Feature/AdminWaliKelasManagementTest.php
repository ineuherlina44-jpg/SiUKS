<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminWaliKelasManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_and_filter_homeroom_teachers(): void
    {
        $admin = $this->createAdmin();
        $this->createTeacher('Guru Satu', 'guru1@example.test', '10001', 'IPA', 'X IPA 1');
        $this->createTeacher('Guru Dua', 'guru2@example.test', '10002', 'IPS', 'XI IPS 1');

        $this->actingAs($admin)
            ->get(route('admin.wali-kelas.index'))
            ->assertOk()
            ->assertSee('Data Wali Kelas')
            ->assertSee('Guru Satu')
            ->assertSee('Guru Dua')
            ->assertViewHas('totalTeachers', 2)
            ->assertViewHas('activeTeachers', 2);

        $this->actingAs($admin)
            ->get(route('admin.wali-kelas.index', ['search' => 'Guru Satu', 'jurusan' => 'IPA', 'kelas' => 'X IPA 1']))
            ->assertOk()
            ->assertSee('Guru Satu')
            ->assertDontSee('Guru Dua');
    }

    public function test_admin_can_create_edit_and_deactivate_homeroom_teacher_accounts(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.wali-kelas.store'), [
                'name' => 'Guru Baru',
                'email' => 'guru.baru@example.test',
                'password' => 'GuruBaru123',
                'password_confirmation' => 'GuruBaru123',
                'nip' => '10003',
                'jurusan' => 'Bahasa',
                'kelas_dampu' => 'X Bahasa 1',
                'status' => 'Aktif',
            ])
            ->assertRedirect(route('admin.wali-kelas.index'))
            ->assertSessionHas('status', 'Data wali kelas dan akun login berhasil dibuat.');

        $teacherUser = User::where('email', 'guru.baru@example.test')->firstOrFail();
        $this->assertSame('wali_kelas', $teacherUser->role);
        $this->assertTrue(Hash::check('GuruBaru123', $teacherUser->password));
        $teacherId = DB::table('wali_kelas')->where('id_user', $teacherUser->id)->value('id');

        $this->actingAs($admin)
            ->put(route('admin.wali-kelas.update', $teacherId), [
                'name' => 'Guru Diperbarui',
                'email' => 'guru.updated@example.test',
                'nip' => '10004',
                'jurusan' => 'IPA',
                'kelas_dampu' => 'XI IPA 2',
                'status' => 'Nonaktif',
            ])
            ->assertRedirect(route('admin.wali-kelas.index'))
            ->assertSessionHas('status', 'Data wali kelas berhasil diperbarui.');

        $this->assertDatabaseHas('users', ['id' => $teacherUser->id, 'name' => 'Guru Diperbarui', 'email' => 'guru.updated@example.test']);
        $this->assertDatabaseHas('wali_kelas', [
            'id' => $teacherId,
            'nip' => '10004',
            'jurusan' => 'IPA',
            'kelas_dampu' => 'XI IPA 2',
            'status' => 'Nonaktif',
        ]);

        $this->post(route('login.store'), [
            'role' => 'wali_kelas',
            'identifier' => 'guru.updated@example.test',
            'password' => 'GuruBaru123',
        ])->assertSessionHasErrors(['identifier' => 'Akun wali kelas ini sedang nonaktif.']);
    }

    public function test_admin_gets_a_clear_warning_when_homeroom_teacher_email_is_already_registered(): void
    {
        $admin = $this->createAdmin();
        $this->createUser('Siswa', 'existing@example.test', 'siswa');

        $this->actingAs($admin)
            ->post(route('admin.wali-kelas.store'), [
                'name' => 'Guru Baru',
                'email' => 'existing@example.test',
                'password' => 'GuruBaru123',
                'password_confirmation' => 'GuruBaru123',
                'nip' => '10010',
                'jurusan' => 'IPA',
                'kelas_dampu' => 'X IPA 1',
                'status' => 'Aktif',
            ])
            ->assertSessionHasErrors(['email' => 'Email ini sudah terdaftar.']);
    }

    public function test_admin_cannot_delete_homeroom_teacher_that_reported_sos(): void
    {
        $admin = $this->createAdmin();
        $teacherId = $this->createTeacher('Guru Pelapor', 'pelapor@example.test', '10005', 'IPA', 'X IPA 2');
        $teacherUserId = DB::table('wali_kelas')->where('id', $teacherId)->value('id_user');
        DB::table('sinyal_darurat')->insert([
            'id_siswa' => $this->createStudent(),
            'id_pelapor' => $teacherUserId,
            'waktu_pemicu' => now(),
            'status_respon' => 'menunggu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.wali-kelas.destroy', $teacherId))
            ->assertRedirect(route('admin.wali-kelas.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $teacherUserId]);
        $this->assertDatabaseHas('wali_kelas', ['id' => $teacherId]);

        $cleanTeacherId = $this->createTeacher('Guru Tanpa SOS', 'bersih@example.test', '10006', 'IPS', 'X IPS 1');
        $cleanUserId = DB::table('wali_kelas')->where('id', $cleanTeacherId)->value('id_user');
        $this->actingAs($admin)
            ->delete(route('admin.wali-kelas.destroy', $cleanTeacherId))
            ->assertRedirect(route('admin.wali-kelas.index'))
            ->assertSessionHas('status', 'Data wali kelas berhasil dihapus.');

        $this->assertDatabaseMissing('wali_kelas', ['id' => $cleanTeacherId]);
        $this->assertDatabaseMissing('users', ['id' => $cleanUserId]);
    }

    public function test_non_admin_cannot_manage_homeroom_teacher_accounts(): void
    {
        $teacher = User::create([
            'name' => 'Wali Kelas',
            'email' => 'wali@example.test',
            'role' => 'wali_kelas',
            'password' => 'password123',
        ]);

        $this->actingAs($teacher)
            ->get(route('admin.wali-kelas.index'))
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

    private function createUser(string $name, string $email, string $role): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => 'password123',
        ]);
    }

    private function createTeacher(string $name, string $email, string $nip, string $major, string $class): int
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'role' => 'wali_kelas',
            'password' => 'password123',
        ]);

        return DB::table('wali_kelas')->insertGetId([
            'id_user' => $user->id,
            'nip' => $nip,
            'jurusan' => $major,
            'kelas_dampu' => $class,
            'status' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createStudent(): int
    {
        $user = User::create([
            'name' => 'Siswa Pelapor SOS',
            'email' => 'siswa.sos@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);

        return DB::table('siswa')->insertGetId([
            'id_user' => $user->id,
            'nisn' => '3000000001',
            'kelas' => 'X IPA 2',
            'jenis_kelamin' => 'Laki-laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
