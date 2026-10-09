<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petugas_pmr', function (Blueprint $table) {
            $table->string('nis')->nullable()->unique();
            $table->string('kelas')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('status')->default('Aktif');
        });
    }

    public function down(): void
    {
        Schema::table('petugas_pmr', function (Blueprint $table) {
            $table->dropUnique(['nis']);
            $table->dropColumn(['nis', 'kelas', 'jenis_kelamin', 'status']);
        });
    }
};
