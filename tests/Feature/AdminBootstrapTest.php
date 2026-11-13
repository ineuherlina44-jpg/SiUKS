<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class AdminBootstrapTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_admin_can_be_created_with_the_interactive_command(): void
    {
        $this->artisan('siuks:admin:create')
            ->expectsQuestion('Administrator name', 'First Admin')
            ->expectsQuestion('Administrator email', 'first-admin@example.test')
            ->expectsQuestion('Password (at least 12 characters)', 'long-password-123')
            ->expectsQuestion('Confirm password', 'long-password-123')
            ->expectsOutput('Administrator account created. You can now sign in.')
            ->assertExitCode(0);

        $admin = User::where('email', 'first-admin@example.test')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('long-password-123', $admin->password));
    }

    public function test_bootstrap_command_refuses_to_create_another_admin(): void
    {
        User::create([
            'name' => 'Existing Admin',
            'email' => 'existing-admin@example.test',
            'role' => 'admin',
            'password' => 'long-password-123',
        ]);

        $this->artisan('siuks:admin:create')
            ->expectsOutput('An administrator already exists. This command only creates the first administrator.')
            ->assertExitCode(1);
    }

    public function test_demo_accounts_are_not_seeded_outside_local_or_testing(): void
    {
        $this->app->detectEnvironment(static fn () => 'production');

        $this->artisan('db:seed', [
            '--class' => DatabaseSeeder::class,
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertDatabaseMissing('users', ['email' => 'admin@siuks.com']);
    }
}
