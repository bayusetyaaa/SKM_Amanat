<?php

use App\Http\Controllers\Admin\CalonAnggotaController as AdminCalonAnggotaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HasilRekomendasiController as AdminHasilRekomendasiController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\KriteriaController as AdminKriteriaController;
use App\Http\Controllers\Admin\MagangSpesialisController as AdminMagangSpesialisController;
use App\Http\Controllers\Admin\NilaiEvaluasiController as AdminNilaiEvaluasiController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;
use App\Http\Controllers\Admin\PenugasanController as AdminPenugasanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\HasilRekomendasiController as MemberHasilRekomendasiController;
use App\Http\Controllers\Member\InfoPendaftaranController as MemberInfoPendaftaranController;
use App\Http\Controllers\Member\MagangSpesialisController as MemberMagangSpesialisController;
use App\Http\Controllers\Member\PengumumanController as MemberPengumumanController;
use App\Http\Controllers\Member\PenugasanController as MemberPenugasanController;
use App\Http\Controllers\Member\PresensiController as MemberPresensiController;
use App\Http\Controllers\Member\ProfilController as MemberProfilController;
use Illuminate\Support\Facades\Route;

// File Streaming Routes (Database-backed files)
Route::middleware(['auth'])->group(function () {
    Route::get('/berkas/{id}/file', [FileController::class, 'viewBerkas'])->name('berkas.file');
    Route::get('/penugasan-file/{id}', [FileController::class, 'viewPengumpulanTugas'])->name('penugasan.file');
    Route::get('/pengumuman/{id}/lampiran', [FileController::class, 'viewPengumumanLampiran'])->name('pengumuman.lampiran');
});

// Redirect Root to Login
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('member.dashboard');
    }
    return redirect()->route('login');
});


// Debug route (temporary - untuk diagnosa error di Vercel)
Route::get('/debug-info', function () {
    try {
        $dbOk = false;
        $dbError = null;
        $tables = [];
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $dbOk = true;
            $tables = \Illuminate\Support\Facades\DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public'");
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        return response()->json([
            'status' => 'ok',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_url' => config('app.url'),
            'db_connection' => config('database.default'),
            'db_host' => config('database.connections.pgsql.host'),
            'db_port' => config('database.connections.pgsql.port'),
            'db_database' => config('database.connections.pgsql.database'),
            'db_username' => config('database.connections.pgsql.username'),
            'db_connected' => $dbOk,
            'db_error' => $dbError,
            'tables' => array_column($tables, 'tablename'),
            'session_driver' => config('session.driver'),
            'session_secure' => config('session.secure'),
            'session_same_site' => config('session.same_site'),
            'storage_path' => storage_path(),
            'view_path' => config('view.compiled'),
            'tmp_writable' => is_writable('/tmp'),
            'tmp_storage_exists' => is_dir('/tmp/storage'),
            'migration_lock' => file_exists('/tmp/storage/migrations_ran.lock') ? file_get_contents('/tmp/storage/migrations_ran.lock') : 'NOT RUN',
        ]);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// OTP & Forgot Password Routes
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify-otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-verify-otp', [AuthController::class, 'resendVerifyOtp'])->name('resend-verify-otp');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendResetOtp']);
Route::post('/resend-reset-otp', [AuthController::class, 'resendResetOtp'])->name('resend-reset-otp');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('reset-password');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Calon Anggota (Member) Routes
Route::middleware(['auth', 'role:calon_anggota'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    
    // Profil & Upload Berkas Persyaratan
    Route::get('/profil', [MemberProfilController::class, 'index'])->name('profil');
    Route::post('/profil/data-pribadi', [MemberProfilController::class, 'updateDataPribadi'])->name('profil.update');
    Route::post('/profil/upload-berkas', [MemberProfilController::class, 'uploadBerkas'])->name('profil.upload');
    
    // Penugasan
    Route::get('/penugasan', [MemberPenugasanController::class, 'index'])->name('penugasan');
    Route::post('/penugasan/{id}/upload', [MemberPenugasanController::class, 'uploadTugas'])->name('penugasan.upload');
    
    // Presensi Kegiatan
    Route::get('/presensi', [MemberPresensiController::class, 'index'])->name('presensi');
    Route::post('/presensi/{kegiatanId}', [MemberPresensiController::class, 'submitPresensi'])->name('presensi.submit');
    
    // Magang Spesialis / Peminatan Divisi
    Route::get('/magang-spesialis', [MemberMagangSpesialisController::class, 'index'])->name('magang-spesialis');
    Route::post('/magang-spesialis', [MemberMagangSpesialisController::class, 'simpanPilihan'])->name('magang-spesialis.simpan');
    
    // Pengumuman
    Route::get('/pengumuman', [MemberPengumumanController::class, 'index'])->name('pengumuman');
    Route::get('/pengumuman/{id}', [MemberPengumumanController::class, 'show'])->name('pengumuman.show');
    
    // Info Pendaftaran & Alur
    Route::get('/info-pendaftaran', [MemberInfoPendaftaranController::class, 'index'])->name('info-pendaftaran');
    
    // Hasil Rekomendasi Divisi
    Route::get('/hasil-rekomendasi', [MemberHasilRekomendasiController::class, 'index'])->name('hasil-rekomendasi');
});

// Admin / Pengurus Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Data Calon Anggota (Cakruma) & Verifikasi Dokumen
    Route::get('/calon-anggota', [AdminCalonAnggotaController::class, 'index'])->name('calon-anggota');
    Route::post('/calon-anggota/berkas/{id}/verifikasi', [AdminCalonAnggotaController::class, 'verifikasiBerkas'])->name('calon-anggota.verifikasi');
    Route::post('/calon-anggota/{userId}/status', [AdminCalonAnggotaController::class, 'updateStatusSeleksi'])->name('calon-anggota.status');
    
    // Magang Spesialis / Data Magang Spesialis
    Route::get('/mapping-spesialis', [AdminMagangSpesialisController::class, 'index'])->name('mapping-spesialis');
    Route::post('/mapping-spesialis/{userId}/update', [AdminMagangSpesialisController::class, 'updateKeputusan'])->name('mapping-spesialis.update');
    
    // Kelola Kegiatan & Presensi
    Route::get('/kegiatan', [AdminKegiatanController::class, 'index'])->name('kegiatan');
    Route::post('/kegiatan', [AdminKegiatanController::class, 'store'])->name('kegiatan.store');
    Route::get('/kegiatan/{id}', [AdminKegiatanController::class, 'detail'])->name('kegiatan.detail');
    Route::post('/kegiatan/{id}/regenerate-token', [AdminKegiatanController::class, 'regenerateToken'])->name('kegiatan.regenerate-token');
    Route::post('/kegiatan/{id}/presensi', [AdminKegiatanController::class, 'updatePresensi'])->name('kegiatan.presensi');
    Route::delete('/kegiatan/{id}', [AdminKegiatanController::class, 'destroy'])->name('kegiatan.destroy');
    
    // Kelola Penugasan & Nilai Wawancara
    Route::get('/penugasan', [AdminPenugasanController::class, 'index'])->name('penugasan');
    Route::post('/penugasan', [AdminPenugasanController::class, 'store'])->name('penugasan.store');
    Route::get('/penugasan/{id}', [AdminPenugasanController::class, 'detail'])->name('penugasan.detail');
    Route::post('/penugasan/pengumpulan/{id}/nilai', [AdminPenugasanController::class, 'beriNilai'])->name('penugasan.nilai');
    Route::post('/penugasan/wawancara/{userId}', [AdminPenugasanController::class, 'simpanNilaiWawancara'])->name('penugasan.wawancara');
    Route::post('/penugasan/wawancara-massal', [AdminPenugasanController::class, 'simpanNilaiWawancaraMassal'])->name('penugasan.wawancara-massal');
    Route::delete('/penugasan/{id}', [AdminPenugasanController::class, 'destroy'])->name('penugasan.destroy');
    
    // Konfigurasi Kriteria & Profil Target
    Route::get('/konfigurasi-kriteria', [AdminKriteriaController::class, 'index'])->name('konfigurasi-kriteria');
    Route::post('/konfigurasi-kriteria', [AdminKriteriaController::class, 'updateTarget'])->name('konfigurasi-kriteria.update');
    
    // Input Nilai Evaluasi (Matrix 7 Kriteria) & Proses Profile Matching
    Route::get('/input-nilai', [AdminNilaiEvaluasiController::class, 'index'])->name('input-nilai');
    Route::post('/input-nilai', [AdminNilaiEvaluasiController::class, 'simpanNilai'])->name('input-nilai.simpan');
    Route::post('/input-nilai/proses', [AdminNilaiEvaluasiController::class, 'prosesProfileMatching'])->name('input-nilai.proses');
    
    // Hasil Rekomendasi & Penetapan Kepengurusan
    Route::get('/hasil-rekomendasi', [AdminHasilRekomendasiController::class, 'index'])->name('hasil-rekomendasi');
    Route::get('/hasil-rekomendasi/detail/{userId}', [AdminHasilRekomendasiController::class, 'detailUser'])->name('hasil-rekomendasi.detail');
    Route::post('/hasil-rekomendasi/simpan-keputusan', [AdminHasilRekomendasiController::class, 'simpanKeputusan'])->name('hasil-rekomendasi.simpan-keputusan');
    Route::get('/hasil-rekomendasi/cetak-laporan', [AdminHasilRekomendasiController::class, 'cetakLaporan'])->name('hasil-rekomendasi.cetak');
    
    // Kelola Pengumuman
    Route::get('/pengumuman', [AdminPengumumanController::class, 'index'])->name('pengumuman');
    Route::post('/pengumuman', [AdminPengumumanController::class, 'store'])->name('pengumuman.store');
    Route::delete('/pengumuman/{id}', [AdminPengumumanController::class, 'destroy'])->name('pengumuman.destroy');
});
