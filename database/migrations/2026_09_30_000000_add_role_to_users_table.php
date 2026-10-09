<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('siswa')->after('email');
        });

        DB::table('users')
            ->whereIn('id', DB::table('wali_kelas')->select('id_user'))
            ->update(['role' => 'wali_kelas']);

        DB::table('users')
            ->whereIn('id', DB::table('petugas_pmr')->select('id_user'))
            ->update(['role' => 'petugas_pmr']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
