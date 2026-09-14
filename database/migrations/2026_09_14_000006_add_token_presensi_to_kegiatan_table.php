<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->string('token_presensi', 10)->nullable()->after('tanggal_waktu');
        });

        // Generate token random untuk data kegiatan yang sudah ada
        $kegiatans = DB::table('kegiatan')->get();
        foreach ($kegiatans as $k) {
            DB::table('kegiatan')
                ->where('id', $k->id)
                ->update(['token_presensi' => strtoupper(Str::random(6))]);
        }
    }

    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropColumn('token_presensi');
        });
    }
};
