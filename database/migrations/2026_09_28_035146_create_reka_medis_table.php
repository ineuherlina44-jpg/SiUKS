<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('id_petugas')->constrained('petugas_pmr')->onDelete('cascade');
            $table->date('waktu_pemeriksaan');
            $table->text('keluhan')->nullable();
            $table->text('tindakan')->nullable();
            $table->string('status_penyakit')->nullable();
            $table->enum('status', ['dirawat', 'diizinkan_pulang', 'rujukan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
