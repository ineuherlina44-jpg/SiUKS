<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('Demo data skipped outside local/testing. Create the first admin with php artisan siuks:admin:create.');

            return;
        }

        $this->call([
            DemoDataSeeder::class,
        ]);
    }
}
