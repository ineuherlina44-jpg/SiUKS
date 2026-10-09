<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPmrManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_filter_and_view_pmr_staff(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        $this->createStaff('Petugas Satu', 'pmr1@example.test', 'PMR001', 'XI IPA 1');
        $this->createStaff('Petugas Dua', 'pmr2@example.test', 'PMR002', 'X IPS 1', 'Nonaktif');

        $this->actingAs($admin)
            ->get(route('admin.pmr.index'))
            ->assertOk()
            ->assertSee('Data Petugas PMR')
            ->assertSee('Petugas Satu')
            ->assertSee('Petugas Dua')
            ->assertViewHas('totalStaff', 2)
            ->assertViewHas('activeStaff', 1);

        $this->actingAs($admin)
            ->get(route('admin.pmr.index', [
                'search' => 'PMR001',
                'kelas' => 'XI IPA 1',
                'status' => 'Aktif',
            ]))
            ->assertOk()
            ->assertSee('Petugas Satu')
            ->assertDontSee('Petugas Dua');
    }

    public function test_admin_can_create_and_edit_pmr_staff_accounts(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');

        $this->actingAs($admin)
            ->post(route('admin.pmr.store'), [
                'name' => 'Petugas Baru',
                'email' => 'pmr.baru@example.test',
                'password' => 'PetugasBaru123',
                'password_confirmation' => 'PetugasBaru123',
                'nis' => 'PMR003',
                'kelas' => 'XI IPA 2',
                'jenis_kelamin' => 'Perempuan',
                'status' => 'Aktif',
            ])
            ->assertRedirect(route('admin.pmr.index'))
            ->assertSessionHas('status', 'Data petugas PMR dan akun login berhasil dibuat.');

        $staffUser = User::where('email', 'pmr.baru@example.test')->firstOrFail();
        $this->assertSame('petugas_pmr', $staffUser->role);
        $this->assertTrue(Hash::check('PetugasBaru123', $staffUser->password));
        $staffId = DB::table('petugas_pmr')->where('id_user', $staffUser->id)->value('id');
        $this->assertDatabaseHas('petugas_pmr', [
            'id' => $staffId,
            'nis' => 'PMR003',
            'kelas' => 'XI IPA 2',
            'jenis_kelamin' => 'Perempuan',
            'status' => 'Aktif',
            'status_piket' => 'Belum dijadwalkan',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.pmr.update', $staffId), [
                'name' => 'Petugas Diperbarui',
                'email' => 'pmr.updated@example.test',
                'nis' => 'PMR004',
                'kelas' => 'XII IPA 1',
                'jenis_kelamin' => 'Laki-laki',
                'status' => 'Nonaktif',
            ])
            ->assertRedirect(route('admin.pmr.index'))
            ->assertSessionHas('status', 'Data petugas PMR berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id' => $staffUser->id,
            'name' => 'Petugas Diperbarui',
            'email' => 'pmr.updated@example.test',
        ]);
        $this->assertDatabaseHas('petugas_pmr', [
            'id' => $staffId,
            'nis' => 'PMR004',
            'kelas' => 'XII IPA 1',
            'status' => 'Nonaktif',
        ]);
    }

    public function test_nonactive_pmr_cannot_login_and_staff_with_history_cannot_be_deleted(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        [$staffId, $staffUserId] = $this->createStaff('Petugas Berstatus', 'pmr-status@example.test', 'PMR005', 'X IPA 1', 'Nonaktif', 'Rahasia123');

        $this->post(route('login.store'), [
            'role' => 'petugas_pmr',
            'identifier' => 'pmr-status@example.test',
            'password' => 'Rahasia123',
        ])->assertSessionHasErrors(['identifier' => 'Akun petugas PMR ini sedang nonaktif.']);

        $studentUser = $this->createUser('Siswa', 'siswa@example.test', 'siswa');
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '3000000001',
            'kelas' => 'X IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('rekam_medis')->insert([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => now(),
            'status' => 'diizinkan_pulang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.pmr.destroy', $staffId))
            ->assertRedirect(route('admin.pmr.index'))
            ->assertSessionHas('error', 'Petugas tidak dapat dihapus karena sudah memiliki catatan rekam medis atau laporan SOS.');

        $this->assertDatabaseHas('users', ['id' => $staffUserId]);
        $this->assertDatabaseHas('petugas_pmr', ['id' => $staffId]);
    }

    public function test_admin_can_delete_staff_without_health_or_sos_history(): void
    {
        $admin = $this->createUser('Admin', 'admin@example.test', 'admin');
        [$staffId, $staffUserId] = $this->createStaff('Petugas Lama', 'pmr-lama@example.test', 'PMR006', 'XI IPS 1');

        $this->actingAs($admin)
            ->delete(route('admin.pmr.destroy', $staffId))
            ->assertRedirect(route('admin.pmr.index'))
            ->assertSessionHas('status', 'Data petugas PMR berhasil dihapus.');

        $this->assertDatabaseMissing('petugas_pmr', ['id' => $staffId]);
        $this->assertDatabaseMissing('users', ['id' => $staffUserId]);
    }

    public function test_non_admin_cannot_manage_pmr_staff(): void
    {
        $wali = $this->createUser('Wali Kelas', 'wali@example.test', 'wali_kelas');

        $this->actingAs($wali)
            ->get(route('admin.pmr.index'))
            ->assertForbidden();
        $this->actingAs($wali)
            ->post(route('admin.pmr.store'), [])
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

    private function createStaff(
        string $name,
        string $email,
        string $nis,
        string $class,
        string $status = 'Aktif',
        string $password = 'password123'
    ): array {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'role' => 'petugas_pmr',
            'password' => $password,
        ]);
        $staffId = DB::table('petugas_pmr')->insertGetId([
            'id_user' => $user->id,
            'nis' => $nis,
            'kelas' => $class,
            'jenis_kelamin' => 'Laki-laki',
            'status' => $status,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$staffId, $user->id];
    }
}
