<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('siswa', 'nomor_whatsapp_orang_tua')) {
            return;
        }

        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nomor_whatsapp_orang_tua', 25)->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('siswa', 'nomor_whatsapp_orang_tua')) {
            return;
        }

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('nomor_whatsapp_orang_tua');
        });
    }
};
