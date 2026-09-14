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
            $table->enum('seleksi_administrasi', ['lolos', 'tidak_lolos'])->nullable()->after('pilihan_divisi_awal');
            $table->enum('tes_tulis_wawancara', ['lolos', 'tidak_lolos'])->nullable()->after('seleksi_administrasi');
            $table->enum('cakruma', ['lolos', 'tidak_lolos'])->nullable()->after('tes_tulis_wawancara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_calon_anggota', function (Blueprint $table) {
            $table->dropColumn(['seleksi_administrasi', 'tes_tulis_wawancara', 'cakruma']);
        });
    }
};
