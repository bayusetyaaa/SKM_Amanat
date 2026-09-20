<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    private function checkIsNotTidakLolos($user): bool
    {
        $user->loadMissing(['profil', 'berkas']);
        $admStatus = $user->profil->seleksi_administrasi ?? null;
        $tesStatus = $user->profil->tes_tulis_wawancara ?? null;
        $cakStatus = $user->profil->cakruma ?? null;
        $hasRejected = $user->berkas->some(fn($b) => $b->status === 'ditolak');

        return !($hasRejected || $admStatus === 'tidak_lolos' || $tesStatus === 'tidak_lolos' || $cakStatus === 'tidak_lolos');
    }

    public function index()
    {
        $user = Auth::user();

        if (!$this->checkIsNotTidakLolos($user)) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Akses Ditolak: Menu Presensi Kegiatan tidak tersedia untuk akun yang tidak lolos seleksi.');
        }

        // Cari kegiatan hari ini
        $kegiatanHariIni = Kegiatan::whereDate('tanggal_waktu', Carbon::today())
            ->orderBy('tanggal_waktu', 'asc')
            ->first();

        // Jika tidak ada kegiatan pas hari ini, cari kegiatan terdekat yang belum lampau
        if (!$kegiatanHariIni) {
            $kegiatanHariIni = Kegiatan::where('tanggal_waktu', '>=', Carbon::now()->startOfDay())
                ->orderBy('tanggal_waktu', 'asc')
                ->first();
        }

        // Cek status presensi user pada kegiatan hari ini
        $presensiHariIni = null;
        if ($kegiatanHariIni) {
            $presensiHariIni = Presensi::where('user_id', $user->id)
                ->where('kegiatan_id', $kegiatanHariIni->id)
                ->first();
        }

        // Riwayat kehadiran user
        $riwayatPresensi = Presensi::with('kegiatan')
            ->where('user_id', $user->id)
            ->orderByDesc('waktu_hadir')
            ->paginate(10)
            ->withQueryString();

        return view('user.presensi', compact('kegiatanHariIni', 'presensiHariIni', 'riwayatPresensi'));
    }

    public function submitPresensi(Request $request, $kegiatanId)
    {
        $user = Auth::user();

        if (!$this->checkIsNotTidakLolos($user)) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Akses Ditolak: Anda tidak dapat melakukan presensi kegiatan.');
        }

        $request->validate([
            'token_presensi' => 'required|string',
        ], [
            'token_presensi.required' => 'Mohon masukkan token presensi yang diberikan oleh panitia/pengurus.',
        ]);

        $kegiatan = Kegiatan::findOrFail($kegiatanId);

        // Validasi kecocokan token presensi
        if (strtoupper(trim($request->token_presensi)) !== strtoupper(trim($kegiatan->token_presensi))) {
            return back()->with('error', 'Token presensi salah! Silakan periksa kembali token kegiatan dari panitia/pengurus.');
        }

        $existing = Presensi::where('user_id', $user->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($existing) {
            return back()->with('info', 'Anda sudah melakukan presensi untuk kegiatan ini.');
        }

        Presensi::create([
            'user_id' => $user->id,
            'kegiatan_id' => $kegiatan->id,
            'waktu_hadir' => Carbon::now(),
            'status' => 'Hadir',
            'keterangan' => 'Presensi mandiri via token web SKM Amanat',
        ]);

        $evalService = new \App\Services\EvaluasiNilaiService();
        $evalService->syncUserScores($user);

        $pmService = new \App\Services\ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return back()->with('success', 'Presensi berhasil dicatat! Kehadiran Anda telah terverifikasi dengan token.');
    }
}
