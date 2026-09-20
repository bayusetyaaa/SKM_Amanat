<?php

namespace App\Services;

use App\Models\Divisi;
use App\Models\HasilProfileMatching;
use App\Models\Kriteria;
use App\Models\NilaiEvaluasi;
use App\Models\ProfilCalonAnggota;
use App\Models\ProfilTarget;
use App\Models\User;

class ProfileMatchingService
{
    /**
     * Hitung bobot dari nilai gap dengan formula kontinu (Opsi A)
     * Bobot = max(0, 100 - |Gap|)
     */
    public function convertGapToBobot(float $gap): float
    {
        return max(0.0, 100.0 - abs($gap));
    }

    /**
     * Jalankan kalkulasi Profile Matching untuk satu user
     */
    public function calculateForUser(int $userId, float $coreWeight = 0.60, float $secondaryWeight = 0.40): array
    {
        $divisis = Divisi::with(['profilTarget.kriteria'])->get();
        $nilaiEvaluasi = NilaiEvaluasi::where('user_id', $userId)
            ->pluck('nilai_aktual', 'kriteria_id')
            ->toArray();

        $results = [];
        $highestScore = -1;
        $recommendedDivisiId = null;

        foreach ($divisis as $divisi) {
            $coreBobotSum = 0;
            $coreCount = 0;
            $secondaryBobotSum = 0;
            $secondaryCount = 0;
            $detailKriteria = [];

            foreach ($divisi->profilTarget as $target) {
                $kriteriaId = $target->kriteria_id;
                $targetNilai = (float) $target->nilai_target;
                $aktualNilai = isset($nilaiEvaluasi[$kriteriaId]) ? (float) $nilaiEvaluasi[$kriteriaId] : 0.0;

                $gap = $aktualNilai - $targetNilai;
                $bobot = $this->convertGapToBobot($gap);

                $detailKriteria[] = [
                    'kriteria_id' => $kriteriaId,
                    'kode' => $target->kriteria->kode ?? ('K' . $kriteriaId),
                    'nama' => $target->kriteria->nama ?? '',
                    'aspek' => $target->kriteria->aspek ?? '',
                    'faktor' => $target->faktor,
                    'target' => $targetNilai,
                    'aktual' => $aktualNilai,
                    'gap' => $gap,
                    'bobot' => $bobot,
                ];

                if ($target->faktor === 'core') {
                    $coreBobotSum += $bobot;
                    $coreCount++;
                } else {
                    $secondaryBobotSum += $bobot;
                    $secondaryCount++;
                }
            }

            $ncf = $coreCount > 0 ? round($coreBobotSum / $coreCount, 2) : 0.0;
            $nsf = $secondaryCount > 0 ? round($secondaryBobotSum / $secondaryCount, 2) : 0.0;
            $nilaiTotal = round(($coreWeight * $ncf) + ($secondaryWeight * $nsf), 2);

            $results[$divisi->id] = [
                'divisi_id' => $divisi->id,
                'divisi_nama' => $divisi->nama,
                'ncf' => $ncf,
                'nsf' => $nsf,
                'nilai_total' => $nilaiTotal,
                'details' => $detailKriteria,
            ];

            if ($nilaiTotal > $highestScore) {
                $highestScore = $nilaiTotal;
                $recommendedDivisiId = $divisi->id;
            }
        }

        // Simpan atau update ke database tabel hasil_profile_matching
        foreach ($results as $divisiId => $res) {
            $isRecommended = ($divisiId === $recommendedDivisiId);
            HasilProfileMatching::updateOrCreate(
                [
                    'user_id' => $userId,
                    'divisi_id' => $divisiId,
                ],
                [
                    'ncf' => $res['ncf'],
                    'nsf' => $res['nsf'],
                    'nilai_total' => $res['nilai_total'],
                    'rekomendasi' => \Illuminate\Support\Facades\DB::raw($isRecommended ? 'true' : 'false'),
                ]
            );
        }

        return [
            'results' => $results,
            'recommended_divisi_id' => $recommendedDivisiId,
        ];
    }

    /**
     * Jalankan kalkulasi rincian Profile Matching murni di memory tanpa query database berulang atau DB write
     */
    public function calculateDetailsInMemory(User $user, $divisis, float $coreWeight = 0.60, float $secondaryWeight = 0.40): array
    {
        $nilaiEvaluasi = $user->nilaiEvaluasi ? $user->nilaiEvaluasi->pluck('nilai_aktual', 'kriteria_id')->toArray() : [];

        $results = [];
        $highestScore = -1;
        $recommendedDivisiId = null;

        foreach ($divisis as $divisi) {
            $coreBobotSum = 0;
            $coreCount = 0;
            $secondaryBobotSum = 0;
            $secondaryCount = 0;
            $detailKriteria = [];

            foreach ($divisi->profilTarget as $target) {
                $kriteriaId = $target->kriteria_id;
                $targetNilai = (float) $target->nilai_target;
                $aktualNilai = isset($nilaiEvaluasi[$kriteriaId]) ? (float) $nilaiEvaluasi[$kriteriaId] : 0.0;

                $gap = $aktualNilai - $targetNilai;
                $bobot = $this->convertGapToBobot($gap);

                $detailKriteria[] = [
                    'kriteria_id' => $kriteriaId,
                    'kode' => $target->kriteria->kode ?? ('K' . $kriteriaId),
                    'nama' => $target->kriteria->nama ?? '',
                    'aspek' => $target->kriteria->aspek ?? '',
                    'faktor' => $target->faktor,
                    'target' => $targetNilai,
                    'aktual' => $aktualNilai,
                    'gap' => $gap,
                    'bobot' => $bobot,
                ];

                if ($target->faktor === 'core') {
                    $coreBobotSum += $bobot;
                    $coreCount++;
                } else {
                    $secondaryBobotSum += $bobot;
                    $secondaryCount++;
                }
            }

            $ncf = $coreCount > 0 ? round($coreBobotSum / $coreCount, 2) : 0.0;
            $nsf = $secondaryCount > 0 ? round($secondaryBobotSum / $secondaryCount, 2) : 0.0;
            $nilaiTotal = round(($coreWeight * $ncf) + ($secondaryWeight * $nsf), 2);

            $results[$divisi->id] = [
                'divisi_id' => $divisi->id,
                'divisi_nama' => $divisi->nama,
                'ncf' => $ncf,
                'nsf' => $nsf,
                'nilai_total' => $nilaiTotal,
                'details' => $detailKriteria,
            ];

            if ($nilaiTotal > $highestScore) {
                $highestScore = $nilaiTotal;
                $recommendedDivisiId = $divisi->id;
            }
        }

        return [
            'results' => $results,
            'recommended_divisi_id' => $recommendedDivisiId,
        ];
    }

    /**
     * Hitung kalkulasi untuk SEMUA calon anggota dan perbarui ranking
     */
    public function calculateAndRankAll(float $coreWeight = 0.60, float $secondaryWeight = 0.40): array
    {
        $users = User::where('role', 'calon_anggota')->get();
        foreach ($users as $user) {
            // Periksa apakah sudah memiliki nilai evaluasi
            $hasNilai = NilaiEvaluasi::where('user_id', $user->id)->exists();
            if ($hasNilai) {
                $this->calculateForUser($user->id, $coreWeight, $secondaryWeight);
            }
        }

        // Hitung ranking per divisi
        $divisis = Divisi::all();
        foreach ($divisis as $divisi) {
            $rankings = HasilProfileMatching::where('divisi_id', $divisi->id)
                ->orderByDesc('nilai_total')
                ->get();

            $rank = 1;
            foreach ($rankings as $item) {
                $item->ranking = $rank++;
                $item->save();
            }
        }

        // Return rekap ranking keseluruhan
        return HasilProfileMatching::with(['user.profil', 'divisi'])
            ->orderByDesc('nilai_total')
            ->get()
            ->toArray();
    }
}
