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
        // 1. Profil Calon Anggota
        Schema::create('profil_calon_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nim')->nullable();
            $table->string('prodi')->nullable();
            $table->string('angkatan', 10)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('pilihan_divisi_awal', 50)->nullable();
            $table->enum('status_seleksi', ['menunggu', 'lolos', 'tidak_lolos'])->default('menunggu');
            $table->string('keputusan_final', 50)->nullable();
            $table->timestamps();
        });

        // 2. Berkas Persyaratan Pendaftaran
        Schema::create('berkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis_berkas', 100); // "CV", "Pas Foto", "Esai", "Karya Pribadi", "KTM", dll.
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('mime_type', 50)->default('application/pdf');
            $table->unsignedBigInteger('ukuran_file'); // bytes
            $table->enum('status', ['menunggu', 'diverifikasi', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 3. Master Divisi
        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50); // "Redaksi" | "Konten"
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 4. Kriteria Penilaian (7 Kriteria)
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // K1..K7
            $table->string('nama', 100);
            $table->string('aspek', 100)->default('Umum'); // "Aspek 1. Kompetensi Teknis & Kreatif", etc.
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 5. Profil Target per Divisi
        Schema::create('profil_target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id')->constrained('divisi')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriteria')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai_target'); // 1-100
            $table->enum('faktor', ['core', 'secondary'])->default('core');
            $table->timestamps();
            $table->unique(['divisi_id', 'kriteria_id']);
        });

        // 6. Nilai Evaluasi (Input Nilai oleh Pengurus)
        Schema::create('nilai_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriteria')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai_aktual'); // 1-100
            $table->foreignId('input_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'kriteria_id']);
        });

        // 7. Kegiatan
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 100)->default('Kegiatan Magang');
            $table->string('nama', 150);
            $table->string('tempat', 150)->nullable();
            $table->text('deskripsi')->nullable();
            $table->dateTime('tanggal_waktu');
            $table->timestamps();
        });

        // 8. Presensi Kegiatan
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->timestamp('waktu_hadir')->nullable();
            $table->string('status', 20)->default('Hadir'); // "Hadir" | "Izin" | "Tidak Hadir"
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'kegiatan_id']);
        });

        // 9. Penugasan
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 100)->default('Tugas Magang');
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 10. Pengumpulan Tugas
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_id')->constrained('penugasan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('mime_type', 50)->default('application/pdf');
            $table->unsignedBigInteger('ukuran_file');
            $table->enum('status', ['terkumpul', 'terlambat', 'dinilai'])->default('terkumpul');
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
            $table->unique(['penugasan_id', 'user_id']);
        });

        // 11. Hasil Profile Matching
        Schema::create('hasil_profile_matching', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('divisi_id')->constrained('divisi')->cascadeOnDelete();
            $table->decimal('ncf', 6, 2);
            $table->decimal('nsf', 6, 2);
            $table->decimal('nilai_total', 6, 2);
            $table->unsignedSmallInteger('ranking')->nullable();
            $table->boolean('rekomendasi')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'divisi_id']);
        });

        // 12. Pengumuman
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('target_audience', 50)->default('semua'); // "semua", "calon_anggota", "admin"
            $table->text('isi');
            $table->string('lampiran_path')->nullable();
            $table->string('penulis', 100)->default('HRD SKM Amanat');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('hasil_profile_matching');
        Schema::dropIfExists('pengumpulan_tugas');
        Schema::dropIfExists('penugasan');
        Schema::dropIfExists('presensi');
        Schema::dropIfExists('kegiatan');
        Schema::dropIfExists('nilai_evaluasi');
        Schema::dropIfExists('profil_target');
        Schema::dropIfExists('kriteria');
        Schema::dropIfExists('divisi');
        Schema::dropIfExists('berkas');
        Schema::dropIfExists('profil_calon_anggota');
    }
};
