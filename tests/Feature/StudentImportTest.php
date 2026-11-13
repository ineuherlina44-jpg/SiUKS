<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_students_by_selecting_class(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.siswa.import'), [
                'import_type' => 'class',
                'kelas' => 'RPL 2',
            ])
            ->assertRedirect(route('admin.siswa.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'name' => 'Kevin Sanjaya',
            'role' => 'siswa',
        ]);

        $this->assertDatabaseHas('siswa', [
            'kelas' => 'RPL 2',
            'nisn' => '0081234512',
        ]);

        $this->assertGreaterThanOrEqual(10, DB::table('siswa')->where('kelas', 'RPL 2')->count());
    }

    public function test_imported_students_receive_one_time_passwords_and_must_change_them(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-one-time@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.siswa.import'), [
                'import_type' => 'class',
                'kelas' => 'RPL 2',
            ])
            ->assertRedirect(route('admin.siswa.index'));

        $credentials = session('import_credentials');
        $this->assertCount(12, $credentials);
        $this->assertCount(12, array_unique(array_column($credentials, 'password')));
        $this->assertNotSame('password123', $credentials[0]['password']);

        $student = DB::table('siswa')->where('nisn', $credentials[0]['nisn'])->first();
        $studentUser = User::findOrFail($student->id_user);
        $this->assertTrue($studentUser->password_change_required);

        $this->get(route('admin.siswa.index'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee($credentials[0]['password']);
        $this->get(route('admin.siswa.index'))
            ->assertDontSee($credentials[0]['password']);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->post(route('login.store'), [
            'role' => 'siswa',
            'identifier' => $credentials[0]['nisn'],
            'password' => $credentials[0]['password'],
        ])->assertRedirect(route('password.change.show'));

        $this->get(route('dashboard'))
            ->assertRedirect(route('password.change.show'));
        $this->get(route('password.change.show'))
            ->assertOk()
            ->assertSee('Buat password baru');

        $newPassword = 'new-unique-password-123';
        $this->put(route('password.change.update'), [
            'current_password' => $credentials[0]['password'],
            'password' => $credentials[0]['password'],
            'password_confirmation' => $credentials[0]['password'],
        ])->assertSessionHasErrors('password');

        $this->put(route('password.change.update'), [
            'current_password' => $credentials[0]['password'],
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertRedirect(route('dashboard'));

        $this->assertFalse($studentUser->fresh()->password_change_required);
        $this->assertTrue(Hash::check($newPassword, $studentUser->fresh()->password));
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_production_student_import_uses_csv_and_blocks_demo_rosters(): void
    {
        config(['app.env' => 'production']);

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-production-import@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.siswa.import'), [
                'import_type' => 'class',
                'kelas' => 'RPL 2',
            ])
            ->assertRedirect(route('admin.siswa.index'))
            ->assertSessionHas('error');

        $csv = UploadedFile::fake()->createWithContent(
            'students.csv',
            "nisn,nama,kelas,jenis_kelamin,riwayat_alergi\n1234500001,Siswa Produksi,RPL 9,Perempuan,Tidak ada\n"
        );

        $this->post(route('admin.siswa.import'), [
            'import_type' => 'csv',
            'csv_file' => $csv,
        ])
            ->assertRedirect(route('admin.siswa.index'))
            ->assertSessionHas('import_credentials', fn (array $credentials): bool => count($credentials) === 1);

        $credentials = session('import_credentials');
        $student = DB::table('siswa')->where('nisn', '1234500001')->first();
        $studentUser = User::findOrFail($student->id_user);

        $this->assertSame('1234500001', $credentials[0]['nisn']);
        $this->assertTrue($studentUser->password_change_required);
        $this->assertTrue(Hash::check($credentials[0]['password'], $studentUser->password));
        $this->assertSame(0, DB::table('siswa')->where('nisn', '0081234512')->count());
    }

    public function test_wali_kelas_can_import_students_for_their_class(): void
    {
        $waliUser = User::create([
            'name' => 'Wali Kelas Test',
            'email' => 'walitest@example.test',
            'role' => 'wali_kelas',
            'password' => 'password123',
        ]);

        DB::table('wali_kelas')->insert([
            'id_user' => $waliUser->id,
            'kelas_dampu' => 'RPL 3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($waliUser)
            ->post(route('wali-kelas.siswa.import'), [
                'import_type' => 'class',
            ])
            ->assertRedirect(route('wali-kelas.siswa.index'))
            ->assertSessionHas('status');

        $this->assertGreaterThanOrEqual(10, DB::table('siswa')->where('kelas', 'RPL 3')->count());
    }

    public function test_admin_can_download_csv_template(): void
    {
        $admin = User::create([
            'name' => 'Admin Test 2',
            'email' => 'admintest2@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.siswa.template', ['kelas' => 'RPL 1']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('nisn,nama,kelas,jenis_kelamin,riwayat_alergi', $response->getContent());
    }
}
