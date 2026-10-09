<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_identifier_matches_the_selected_role_and_keeps_old_input(): void
    {
        $this->withSession([
            '_old_input' => [
                'role' => 'wali_kelas',
                'identifier' => 'wali@example.test',
            ],
        ])
            ->get(route('login'))
            ->assertOk()
            ->assertSee('<label id="login-identifier-label" for="login-identifier">Email</label>', false)
            ->assertSee('type="email"', false)
            ->assertSee('value="wali@example.test"', false)
            ->assertSee('placeholder="nama@contoh.com"', false);
    }

    public function test_student_registration_disables_fields_for_other_roles(): void
    {
        $response = $this->get(route('register.role', 'siswa'))->assertOk();
        $html = $response->getContent();

        $this->assertMatchesRegularExpression('/<input\b(?=[^>]*\bid="nisn")(?=[^>]*\brequired)(?![^>]*\bdisabled)[^>]*>/', $html);
        $this->assertMatchesRegularExpression('/<select\b(?=[^>]*\bid="kelas")(?=[^>]*\bname="kelas")(?=[^>]*\brequired)(?![^>]*\bdisabled)[^>]*>/', $html);
        foreach (['BRP 1', 'BRP 6', 'RPL 1', 'RPL 4', 'DKV 1', 'DKV 3'] as $class) {
            $this->assertStringContainsString('value="'.$class.'"', $html);
        }
        $this->assertMatchesRegularExpression('/<select\b(?=[^>]*\bid="kelas_dampu")(?=[^>]*\brequired)(?=[^>]*\bdisabled)[^>]*>/', $html);
    }

    public function test_protected_staff_roles_can_only_be_created_through_admin_management(): void
    {
        foreach ([
            'petugas_pmr' => 'Akun Petugas PMR hanya dapat dibuat oleh admin setelah identitas dan penugasan diverifikasi.',
            'wali_kelas' => 'Akun Wali Kelas hanya dapat dibuat oleh admin setelah identitas dan kelas dampu diverifikasi.',
        ] as $role => $message) {
            $this->get(route('register.role', $role))
                ->assertOk()
                ->assertSee($message)
                ->assertDontSee('id="name"', false)
                ->assertDontSee('type="submit"', false);

            $this->post(route('register.store', $role))->assertForbidden();
        }

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($admin);
        foreach (['petugas_pmr', 'wali_kelas'] as $role) {
            $this->post(route('register.store', $role))->assertForbidden();
        }
    }

    public function test_admin_registration_only_shows_account_creation_instructions(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('data-role="admin" aria-pressed="false"', false);

        $this->get(route('register.role', 'admin'))
            ->assertOk()
            ->assertSee('Pendaftaran akun Admin hanya bisa dilakukan dengan meminta administrator sekolah membuatkan akun untuk Anda.')
            ->assertDontSee('id="name"', false)
            ->assertDontSee('id="email"', false)
            ->assertDontSee('id="password"', false)
            ->assertDontSee('type="submit"', false);

        $this->post(route('register.store', 'admin'))->assertForbidden();
    }

    public function test_admin_registration_only_shows_account_creation_instructions_to_admin(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->get(route('register.role', 'admin'))
            ->assertOk()
            ->assertSee('data-role="admin" aria-pressed="true"', false)
            ->assertSee('Pendaftaran akun Admin hanya bisa dilakukan dengan meminta administrator sekolah membuatkan akun untuk Anda.')
            ->assertDontSee('id="name"', false)
            ->assertDontSee('type="submit"', false);
    }

    public function test_admin_is_directed_to_the_existing_staff_management_pages(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->get(route('register.role', 'petugas_pmr'))
            ->assertOk()
            ->assertSee(route('admin.pmr.index'), false);

        $this->get(route('register.role', 'wali_kelas'))
            ->assertOk()
            ->assertSee(route('admin.wali-kelas.index'), false);
    }
}
