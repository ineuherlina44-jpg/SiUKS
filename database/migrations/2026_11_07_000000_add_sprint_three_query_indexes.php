<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->index(['kelas', 'id'], 'siswa_kelas_id_index');
        });

        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->index(['id_siswa', 'waktu_pemeriksaan', 'id'], 'rekam_medis_student_date_index');
            $table->index(['status', 'waktu_pemeriksaan'], 'rekam_medis_status_date_index');
            $table->index(['waktu_pemeriksaan', 'id_siswa'], 'rekam_medis_date_student_index');
        });

        Schema::table('sinyal_darurat', function (Blueprint $table) {
            $table->index(['id_siswa', 'status_respon'], 'sinyal_siswa_status_index');
            $table->index(['status_respon', 'waktu_pemicu'], 'sinyal_status_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('sinyal_darurat', function (Blueprint $table) {
            $table->dropIndex('sinyal_siswa_status_index');
            $table->dropIndex('sinyal_status_date_index');
        });

        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropIndex('rekam_medis_student_date_index');
            $table->dropIndex('rekam_medis_status_date_index');
            $table->dropIndex('rekam_medis_date_student_index');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex('siswa_kelas_id_index');
        });
    }
};
