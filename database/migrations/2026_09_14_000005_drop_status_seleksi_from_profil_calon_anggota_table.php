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
        Schema::table('profil_calon_anggota', function (Blueprint $table) {
            if (Schema::hasColumn('profil_calon_anggota', 'status_seleksi')) {
                $table->dropColumn('status_seleksi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_calon_anggota', function (Blueprint $table) {
            $table->enum('status_seleksi', ['menunggu', 'lolos', 'tidak_lolos'])->default('menunggu');
        });
    }
};
