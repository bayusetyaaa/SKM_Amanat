<?php

namespace App\Services;

use App\Models\Kegiatan;
use App\Models\Kriteria;
use App\Models\NilaiEvaluasi;
use App\Models\PengumpulanTugas;
use App\Models\Penugasan;
use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EvaluasiNilaiService
{
    /**
     * Hitung dan sinkronisasi seluruh nilai 7 kriteria untuk seorang user
     */
    public function syncUserScores(User $user, $adminId = null)
    {
        $adminId = $adminId ?? Auth::id() ?? 1;
        $kriterias = Kriteria::all()->keyBy('kode');

        $allPenugasans = Penugasan::all();

        // 1. K1 - Kepenulisan (Tugas Kepenulisan)
        if (isset($kriterias['K1'])) {
            $tugasK1Ids = $allPenugasans->filter(function ($p) {
                return $p->hasIndikator('K1') || 
                    stripos($p->judul, 'straight news') !== false || 
                    stripos($p->judul, 'kepenulisan') !== false;
            })->pluck('id');

            if ($tugasK1Ids->isNotEmpty()) {
                $pengumpulanK1 = PengumpulanTugas::whereIn('penugasan_id', $tugasK1Ids)
                    ->where('user_id', $user->id)
                    ->whereNotNull('nilai')
                    ->get();
                if ($pengumpulanK1->isNotEmpty()) {
                    $scoreK1 = round($pengumpulanK1->avg('nilai'));
                    NilaiEvaluasi::updateOrCreate(
                        ['user_id' => $user->id, 'kriteria_id' => $kriterias['K1']->id],
                        ['nilai_aktual' => $scoreK1, 'input_oleh' => $adminId]
                    );
                }
            }
        }

        // 2. K2 - Kepekaan Isu (Tugas Analisis Isu)
        if (isset($kriterias['K2'])) {
            $tugasK2Ids = $allPenugasans->filter(function ($p) {
                return $p->hasIndikator('K2') || 
                    stripos($p->judul, 'analisis isu') !== false || 
                    stripos($p->judul, 'isu') !== false;
            })->pluck('id');

            if ($tugasK2Ids->isNotEmpty()) {
                $pengumpulanK2 = PengumpulanTugas::whereIn('penugasan_id', $tugasK2Ids)
                    ->where('user_id', $user->id)
                    ->whereNotNull('nilai')
                    ->get();
                if ($pengumpulanK2->isNotEmpty()) {
                    $scoreK2 = round($pengumpulanK2->avg('nilai'));
                    NilaiEvaluasi::updateOrCreate(
                        ['user_id' => $user->id, 'kriteria_id' => $kriterias['K2']->id],
                        ['nilai_aktual' => $scoreK2, 'input_oleh' => $adminId]
                    );
                }
            }
        }

        // 3. K3 - Kreativitas (Tugas Kreatif)
        if (isset($kriterias['K3'])) {
            $tugasK3Ids = $allPenugasans->filter(function ($p) {
                return $p->hasIndikator('K3') || 
                    stripos($p->judul, 'desain') !== false || 
                    stripos($p->judul, 'fotografi') !== false || 
                    stripos($p->judul, 'kreatif') !== false || 
                    stripos($p->judul, 'visual') !== false;
            })->pluck('id');

            if ($tugasK3Ids->isNotEmpty()) {
                $pengumpulanK3 = PengumpulanTugas::whereIn('penugasan_id', $tugasK3Ids)
                    ->where('user_id', $user->id)
                    ->whereNotNull('nilai')
                    ->get();
                if ($pengumpulanK3->isNotEmpty()) {
                    $scoreK3 = round($pengumpulanK3->avg('nilai'));
                    NilaiEvaluasi::updateOrCreate(
                        ['user_id' => $user->id, 'kriteria_id' => $kriterias['K3']->id],
                        ['nilai_aktual' => $scoreK3, 'input_oleh' => $adminId]
                    );
                }
            }
        }

        // 6. K6 - Kedisiplinan (60% Pengumpulan Tugas + 40% Absensi Kegiatan)
        if (isset($kriterias['K6'])) {
            // Komponen Tugas (60%)
            $totalTugas = Penugasan::count();
            if ($totalTugas > 0) {
                $tugasDikumpulkan = PengumpulanTugas::where('user_id', $user->id)
                    ->whereIn('status', ['terkumpul', 'dinilai', 'terlambat'])
                    ->count();
                $skorPengumpulan = min(100, ($tugasDikumpulkan / $totalTugas) * 100);
            } else {
                $skorPengumpulan = 100;
            }

            // Komponen Absensi (40%)
            $totalKegiatan = Kegiatan::where('tanggal_waktu', '<=', Carbon::now())->count();
            if ($totalKegiatan > 0) {
                $totalHadir = Presensi::where('user_id', $user->id)
                    ->where('status', 'Hadir')
                    ->whereHas('kegiatan', function ($q) {
                        $q->where('tanggal_waktu', '<=', Carbon::now());
                    })
                    ->count();
                $skorAbsensi = min(100, ($totalHadir / $totalKegiatan) * 100);
            } else {
                $skorAbsensi = 100;
            }

            $nilaiK6 = round((0.60 * $skorPengumpulan) + (0.40 * $skorAbsensi));
            $nilaiK6 = max(0, min(100, $nilaiK6));

            NilaiEvaluasi::updateOrCreate(
                ['user_id' => $user->id, 'kriteria_id' => $kriterias['K6']->id],
                ['nilai_aktual' => $nilaiK6, 'input_oleh' => $adminId]
            );
        }
    }

    /**
     * Hitung dan sinkronisasi seluruh calon anggota
     */
    public function syncAllScores($adminId = null)
    {
        $users = User::where('role', 'calon_anggota')->get();
        foreach ($users as $user) {
            $this->syncUserScores($user, $adminId);
        }
    }
}

