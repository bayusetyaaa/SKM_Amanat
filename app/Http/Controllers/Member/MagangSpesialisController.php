<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\ProfilCalonAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagangSpesialisController extends Controller
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
        $user = Auth::user()->load('profil');

        if (!$this->checkIsLolos($user)) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Akses Ditolak: Menu Magang Spesialis hanya dapat diakses setelah Anda dinyatakan LOLOS pada seluruh tahapan seleksi.');
        }

        $divisis = Divisi::all();

        return view('user.magang_spesialis', compact('user', 'divisis'));
    }

    public function simpanPilihan(Request $request)
    {
        $user = Auth::user();

        if (!$this->checkIsLolos($user)) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Akses Ditolak: Anda belum dinyatakan lolos pada seluruh tahapan seleksi.');
        }

        $request->validate([
            'pilihan_divisi' => 'required|string|in:Redaksi,Konten',
        ]);

        ProfilCalonAnggota::updateOrCreate(
            ['user_id' => $user->id],
            ['pilihan_divisi_awal' => $request->pilihan_divisi]
        );

        return back()->with('success', "Pilihan peminatan divisi magang ({$request->pilihan_divisi}) berhasil disimpan.");
    }
}
