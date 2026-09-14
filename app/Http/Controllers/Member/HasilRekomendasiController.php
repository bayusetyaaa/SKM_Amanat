<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\HasilProfileMatching;
use App\Models\NilaiEvaluasi;
use App\Services\ProfileMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilRekomendasiController extends Controller
{
    private function checkIsLolos($user): bool
    {
        $user->loadMissing(['profil', 'berkas']);
        $admStatus = $user->profil->seleksi_administrasi ?? null;
        $tesStatus = $user->profil->tes_tulis_wawancara ?? null;
        $cakStatus = $user->profil->cakruma ?? null;
        $hasRejected = $user->berkas->some(fn($b) => $b->status === 'ditolak');

        return ($admStatus === 'lolos' && $tesStatus === 'lolos' && $cakStatus === 'lolos' && !$hasRejected);
    }

    public function index()
    {
        $user = Auth::user()->load(['profil', 'nilaiEvaluasi.kriteria']);

        if (!$this->checkIsLolos($user)) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Akses Ditolak: Menu Hasil Rekomendasi hanya dapat diakses setelah Anda dinyatakan LOLOS pada seluruh tahapan seleksi.');
        }

        $hasilList = HasilProfileMatching::with('divisi')
            ->where('user_id', $user->id)
            ->orderByDesc('nilai_total')
            ->get();

        $rekomendasi = $hasilList->firstWhere('rekomendasi', true);

        // Jika user memiliki nilai tapi belum dihitung di tabel PM, hitung on-the-fly
        if ($hasilList->isEmpty() && $user->nilaiEvaluasi->isNotEmpty()) {
            $pmService = new ProfileMatchingService();
            $calc = $pmService->calculateForUser($user->id);
            $hasilList = HasilProfileMatching::with('divisi')
                ->where('user_id', $user->id)
                ->orderByDesc('nilai_total')
                ->get();
            $rekomendasi = $hasilList->firstWhere('rekomendasi', true);
        }

        return view('user.hasil_rekomendasi', compact('user', 'hasilList', 'rekomendasi'));
    }
}
