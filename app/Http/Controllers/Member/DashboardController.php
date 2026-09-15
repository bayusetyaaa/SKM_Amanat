<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Pengumuman;
use App\Models\Penugasan;
use App\Models\Presensi;
use App\Models\HasilProfileMatching;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['profil', 'berkas']);
        
        // Tugas aktif yang belum dikerjakan / deadline belum lewat
        $tugasAktif = Penugasan::with(['pengumpulanUser' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->orderByDesc('deadline')->take(3)->get();

        // Kegiatan hari ini atau terdekat
        $kegiatanTerdekat = Kegiatan::whereDate('tanggal_waktu', '>=', Carbon::today())
            ->orderBy('tanggal_waktu', 'asc')
            ->first();

        // Cek presensi hari ini
        $presensiHariIni = null;
        if ($kegiatanTerdekat) {
            $presensiHariIni = Presensi::where('user_id', $user->id)
                ->where('kegiatan_id', $kegiatanTerdekat->id)
                ->first();
        }

        // Pengumuman terbaru
        $pengumumanTerbaru = Pengumuman::whereIn('target_audience', ['semua', 'calon_anggota'])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        // Hasil rekomendasi jika ada
        $hasilRekomendasi = HasilProfileMatching::with('divisi')
            ->where('user_id', $user->id)
            ->whereRaw('"rekomendasi" = true')
            ->first();

        return view('user.dashboard', compact(
            'user',
            'tugasAktif',
            'kegiatanTerdekat',
            'presensiHariIni',
            'pengumumanTerbaru',
            'hasilRekomendasi'
        ));
    }
}
