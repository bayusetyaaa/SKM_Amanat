<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\NilaiEvaluasi;
use App\Models\User;
use App\Services\ProfileMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiEvaluasiController extends Controller
{
    public function index()
    {
        $calonAnggotas = User::where('role', 'calon_anggota')
            ->with(['profil', 'nilaiEvaluasi'])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $kriterias = Kriteria::orderBy('kode')->get();

        // Buat map nilai [user_id][kriteria_id] => nilai_aktual
        $nilaiMatrix = [];
        foreach ($calonAnggotas as $ca) {
            foreach ($ca->nilaiEvaluasi as $ne) {
                $nilaiMatrix[$ca->id][$ne->kriteria_id] = $ne->nilai_aktual;
            }
        }

        return view('admin.input_nilai', compact('calonAnggotas', 'kriterias', 'nilaiMatrix'));
    }

    public function simpanNilai(Request $request)
    {
        $request->validate([
            'nilai' => 'required|array',
        ]);

        $adminId = Auth::id();

        foreach ($request->nilai as $userId => $kriteriaValues) {
            foreach ($kriteriaValues as $kriteriaId => $val) {
                if ($val !== null && $val !== '') {
                    $nilai = max(0, min(100, (int) $val));
                    NilaiEvaluasi::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'kriteria_id' => $kriteriaId,
                        ],
                        [
                            'nilai_aktual' => $nilai,
                            'input_oleh' => $adminId,
                        ]
                    );
                }
            }
        }

        return back()->with('success', 'Nilai evaluasi calon anggota berhasil disimpan.');
    }

    public function prosesProfileMatching(Request $request)
    {
        // Simpan nilai terlebih dahulu jika ada request post
        if ($request->has('nilai')) {
            $this->simpanNilai($request);
        }

        $pmService = new ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return redirect()->route('admin.hasil-rekomendasi')
            ->with('success', 'Perhitungan Algoritma Profile Matching berhasil diproses! Hasil perangkingan dan rekomendasi divisi telah diperbarui.');
    }
}
