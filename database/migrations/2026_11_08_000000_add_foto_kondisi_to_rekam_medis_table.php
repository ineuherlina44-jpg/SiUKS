<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('rekam_medis', 'foto_kondisi')) {
            Schema::table('rekam_medis', function (Blueprint $table) {
                $table->string('foto_kondisi')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('rekam_medis', 'foto_kondisi')) {
            Schema::table('rekam_medis', function (Blueprint $table) {
                $table->dropColumn('foto_kondisi');
            });
        }
    }
};
