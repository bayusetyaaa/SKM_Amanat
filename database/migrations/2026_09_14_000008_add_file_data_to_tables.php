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
        Schema::table('berkas', function (Blueprint $table) {
            $table->longText('file_data')->nullable()->after('nama_file');
            $table->string('file_path')->nullable()->change();
        });

        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->longText('file_data')->nullable()->after('nama_file');
            $table->string('file_path')->nullable()->change();
        });

        Schema::table('pengumuman', function (Blueprint $table) {
            $table->longText('lampiran_data')->nullable()->after('lampiran_path');
            $table->string('lampiran_nama')->nullable()->after('lampiran_data');
            $table->string('lampiran_mime', 100)->nullable()->after('lampiran_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropColumn(['lampiran_data', 'lampiran_nama', 'lampiran_mime']);
        });

        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->dropColumn('file_data');
        });

        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn('file_data');
        });
    }
};
