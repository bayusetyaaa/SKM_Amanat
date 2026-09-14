<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\HasilProfileMatching;
use App\Models\ProfilCalonAnggota;
use App\Models\User;
use App\Services\ProfileMatchingService;
use Illuminate\Http\Request;

class HasilRekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $divisis = Divisi::all();
        $query = HasilProfileMatching::with(['user.profil', 'divisi', 'user.nilaiEvaluasi.kriteria']);

        if ($request->filled('divisi') && $request->divisi !== 'Semua Divisi') {
            $divisiNama = $request->divisi;
            $query->whereHas('divisi', function ($q) use ($divisiNama) {
                $q->where('nama', $divisiNama);
            });
        } else {
            // Default hanya tampilkan baris rekomendasi divisi terbaik per user
            $query->where('rekomendasi', true);
        }

        $hasilRankings = $query->orderByDesc('nilai_total')->get();

        $pmService = new ProfileMatchingService();
        $calcDetailsByUser = [];
        foreach ($hasilRankings as $hr) {
            if (!isset($calcDetailsByUser[$hr->user_id])) {
                $calcDetailsByUser[$hr->user_id] = $pmService->calculateForUser($hr->user_id);
            }
        }

        return view('admin.hasil_rekomendasi', compact('divisis', 'hasilRankings', 'calcDetailsByUser'));
    }

    public function detailUser($userId)
    {
        $user = User::with(['profil', 'nilaiEvaluasi.kriteria'])->findOrFail($userId);
        $pmService = new ProfileMatchingService();
        $calcData = $pmService->calculateForUser($userId);

        return response()->json([
            'user' => $user,
            'calc' => $calcData,
        ]);
    }

    public function simpanKeputusan(Request $request)
    {
        $request->validate([
            'keputusan' => 'required|array',
        ]);

        foreach ($request->keputusan as $userId => $divisiNama) {
            ProfilCalonAnggota::updateOrCreate(
                ['user_id' => $userId],
                [
                    'keputusan_final' => $divisiNama,
                ]
            );
        }

        return back()->with('success', 'Keputusan final penempatan pengurus/magang berhasil disimpan dan ditetapkan.');
    }

    public function cetakLaporan(Request $request)
    {
        $divisis = Divisi::all();
        $hasilRankings = HasilProfileMatching::with(['user.profil', 'divisi'])
            ->where('rekomendasi', true)
            ->orderByDesc('nilai_total')
            ->get();

        return view('admin.cetak_laporan', compact('hasilRankings', 'divisis'));
    }
}
