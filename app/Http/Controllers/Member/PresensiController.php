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

        // Validasi jendela waktu presensi
        $now = Carbon::now();
        if ($kegiatan->tanggal_waktu && $now->lt($kegiatan->tanggal_waktu)) {
            return back()->with('error', 'Presensi belum dibuka. Kegiatan dijadwalkan mulai pukul ' . $kegiatan->tanggal_waktu->format('H:i') . ' WIB.');
        }
        if ($kegiatan->tanggal_waktu_selesai && $now->gt($kegiatan->tanggal_waktu_selesai)) {
            return back()->with('error', 'Batas waktu presensi telah berakhir pada pukul ' . $kegiatan->tanggal_waktu_selesai->format('H:i') . ' WIB.');
        }

        // Validasi kecocokan token presensi
        if (strtoupper(trim($request->token_presensi)) !== strtoupper(trim($kegiatan->token_presensi))) {
            return back()->with('error', 'Token presensi salah! Silakan periksa kembali token kegiatan dari panitia/pengurus.');
        }

        $existing = Presensi::where('user_id', $user->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($existing && $existing->status === 'Hadir') {
            return back()->with('info', 'Anda sudah melakukan presensi Hadir untuk kegiatan ini.');
        }

        Presensi::updateOrCreate(
            ['user_id' => $user->id, 'kegiatan_id' => $kegiatan->id],
            [
                'waktu_hadir' => Carbon::now(),
                'status' => 'Hadir',
                'keterangan' => 'Presensi mandiri via token web SKM Amanat',
            ]
        );

        $evalService = new \App\Services\EvaluasiNilaiService();
        $evalService->syncUserScores($user);

        $pmService = new \App\Services\ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return back()->with('success', 'Presensi berhasil dicatat! Kehadiran Anda telah terverifikasi dengan token.');
    }

    public function submitIzinSakit(Request $request, $kegiatanId)
    {
        $user = Auth::user();

        if (!$this->checkIsNotTidakLolos($user)) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Akses Ditolak: Anda tidak dapat mengajukan izin/sakit.');
        }

        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'keterangan' => 'required|string|min:3|max:500',
        ], [
            'status.required' => 'Pilih jenis permohonan (Izin atau Sakit).',
            'keterangan.required' => 'Mohon sertakan alasan / keterangan izin atau sakit.',
            'keterangan.min' => 'Keterangan minimal 3 karakter.',
        ]);

        $kegiatan = Kegiatan::findOrFail($kegiatanId);

        // Validasi jendela waktu — tidak bisa izin/sakit sebelum atau sesudah kegiatan
        $now = Carbon::now();
        if ($kegiatan->tanggal_waktu && $now->lt($kegiatan->tanggal_waktu)) {
            return back()->with('error', 'Kegiatan belum dimulai. Pengajuan Izin/Sakit dibuka mulai pukul ' . $kegiatan->tanggal_waktu->format('H:i') . ' WIB.');
        }
        if ($kegiatan->tanggal_waktu_selesai && $now->gt($kegiatan->tanggal_waktu_selesai)) {
            return back()->with('error', 'Batas waktu pengajuan Izin/Sakit telah berakhir pada pukul ' . $kegiatan->tanggal_waktu_selesai->format('H:i') . ' WIB. Tidak dapat lagi mengajukan permohonan.');
        }

        // Cek apakah sudah pernah presensi Hadir — tidak bisa diganti dengan Izin/Sakit
        $existing = Presensi::where('user_id', $user->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->first();
        if ($existing && $existing->status === 'Hadir') {
            return back()->with('info', 'Anda sudah tercatat Hadir pada kegiatan ini. Tidak perlu mengajukan Izin/Sakit.');
        }

        Presensi::updateOrCreate(
            ['user_id' => $user->id, 'kegiatan_id' => $kegiatan->id],
            [
                'waktu_hadir' => Carbon::now(),
                'status'      => $request->status,
                'keterangan'  => $request->keterangan,
            ]
        );

        $evalService = new \App\Services\EvaluasiNilaiService();
        $evalService->syncUserScores($user);

        $pmService = new \App\Services\ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return back()->with('success', "Permohonan {$request->status} berhasil diajukan dan dicatat ke dalam sistem.");
    }
}
