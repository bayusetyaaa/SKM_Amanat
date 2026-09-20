<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;

class MagangSpesialisController extends Controller
{
    public function index(Request $request)
    {
        $divisis = Divisi::all();
        $query = User::where('role', 'calon_anggota')
            ->whereHas('profil', function ($q) {
                $q->where('seleksi_administrasi', 'lolos')
                  ->where('tes_tulis_wawancara', 'lolos')
                  ->where('cakruma', 'lolos');
            })
            ->whereDoesntHave('berkas', function ($q) {
                $q->where('status', 'ditolak');
            })
            ->with(['profil', 'hasilProfileMatching.divisi']);

        // Filter 1: Divisi Pilihan (Pilihan Awal)
        if ($request->filled('divisi_pilihan') && $request->divisi_pilihan !== 'Semua Pilihan') {
            $pilihan = $request->divisi_pilihan;
            $query->whereHas('profil', function ($q) use ($pilihan) {
                $q->where('pilihan_divisi_awal', $pilihan);
            });
        }

        // Filter 2: Divisi Akhir (Keputusan Final atau Rekomendasi Profile Matching)
        if ($request->filled('divisi_akhir') && $request->divisi_akhir !== 'Semua Divisi Akhir') {
            $akhir = $request->divisi_akhir;
            $query->where(function ($q) use ($akhir) {
                $q->whereHas('profil', function ($qp) use ($akhir) {
                    $qp->where('keputusan_final', $akhir);
                })->orWhere(function ($sub) use ($akhir) {
                    $sub->whereHas('profil', function ($qp) {
                        $qp->whereNull('keputusan_final')
                           ->orWhereNotIn('keputusan_final', ['Redaksi', 'Konten']);
                    })->whereHas('hasilProfileMatching', function ($qpm) use ($akhir) {
                        $qpm->whereRaw('"rekomendasi" = true')
                            ->whereHas('divisi', function ($qd) use ($akhir) {
                                $qd->where('nama', $akhir);
                            });
                    });
                });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profil', function ($qp) use ($search) {
                      $qp->where('nim', 'like', "%{$search}%");
                  });
            });
        }

        $anggotaList = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.magang_spesialis', compact('divisis', 'anggotaList'));
    }

    public function updateKeputusan(Request $request, $userId)
    {
        $request->validate([
            'keputusan_final' => 'required|string|in:Redaksi,Konten'
        ]);

        $user = User::where('role', 'calon_anggota')->findOrFail($userId);
        
        $profil = $user->profil;
        if ($profil) {
            $profil->update([
                'keputusan_final' => $request->keputusan_final
            ]);
        }

        return redirect()->back()->with('success', "Hasil akhir divisi untuk {$user->name} berhasil diubah menjadi Divisi {$request->keputusan_final}.");
    }
}
