<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_saves_data_to_database(): void
    {
        $response = $this->post('/register/siswa', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'nisn' => '1234567890',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'nomor_whatsapp_orang_tua' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'siswa',
        ]);
        $this->assertDatabaseHas('siswa', [
            'nisn' => '1234567890',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'nomor_whatsapp_orang_tua' => '081234567890',
        ]);
        $this->assertNotNull(User::where('email', 'budi@example.com')->first());

        $this->get(route('siswa.riwayat-kesehatan'))
            ->assertOk()
            ->assertSee('Riwayat Kesehatan Saya')
            ->assertSee('Total kunjungan')
            ->assertSee('Belum ada riwayat kunjungan UKS.');
    }

    public function test_student_registration_warns_when_email_or_nisn_is_already_registered(): void
    {
        $existing = User::create([
            'name' => 'Siswa Lama',
            'email' => 'siswa.lama@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        DB::table('siswa')->insert([
            'id_user' => $existing->id,
            'nisn' => '1234567891',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post('/register/siswa', [
            'name' => 'Siswa Baru',
            'email' => 'siswa.lama@example.test',
            'nisn' => '1234567891',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'nomor_whatsapp_orang_tua' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasErrors([
                'email' => 'Email ini sudah terdaftar.',
                'nisn' => 'NISN ini sudah terdaftar.',
            ]);

        $this->assertSame(1, User::where('email', 'siswa.lama@example.test')->count());
    }
}
