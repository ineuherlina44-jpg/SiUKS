<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        ]);
        $this->assertNotNull(User::where('email', 'budi@example.com')->first());

        $this->get(route('siswa.riwayat-kesehatan'))
            ->assertOk()
            ->assertSee('Riwayat Kesehatan Saya')
            ->assertSee('Total kunjungan')
            ->assertSee('Belum ada riwayat kunjungan UKS.');
    }
}
