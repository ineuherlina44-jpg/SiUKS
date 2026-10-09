<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->string('nip')->nullable()->unique();
            $table->string('jurusan')->nullable();
            $table->string('status')->default('Aktif');
        });
    }

    public function down(): void
    {
        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropColumn(['nip', 'jurusan', 'status']);
        });
    }
};
