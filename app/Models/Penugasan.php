<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    use HasFactory;

    protected $table = 'penugasan';

    protected $fillable = [
        'jenis',
        'kriteria_kode',
        'indikator_kriteria',
        'judul',
        'deskripsi',
        'deadline',
        'dibuat_oleh',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'indikator_kriteria' => 'array',
    ];

    protected static function booted()
    {
        static::saved(function ($penugasan) {
            $evalService = app(\App\Services\EvaluasiNilaiService::class);
            $evalService->syncAllScores();

            $pmService = app(\App\Services\ProfileMatchingService::class);
            $pmService->calculateAndRankAll();
        });

        static::deleted(function ($penugasan) {
            $evalService = app(\App\Services\EvaluasiNilaiService::class);
            $evalService->syncAllScores();

            $pmService = app(\App\Services\ProfileMatchingService::class);
            $pmService->calculateAndRankAll();
        });
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class, 'penugasan_id');
    }

    public function pengumpulanUser($userId = null)
    {
        $relation = $this->hasMany(PengumpulanTugas::class, 'penugasan_id');
        if ($userId) {
            $relation->where('user_id', $userId);
        }
        return $relation;
    }

    public function hasIndikator(string $kode): bool
    {
        $normalizedKode = strtoupper($kode);
        $indicators = is_array($this->indikator_kriteria) ? $this->indikator_kriteria : [];

        // Check if $normalizedKode is in array (e.g. 'K1')
        if (in_array($normalizedKode, array_map('strtoupper', $indicators))) {
            return true;
        }

        // Check alias mappings
        $aliasMap = [
            'K1' => ['KEPENULISAN', 'STRAIGHT NEWS', 'MENULIS'],
            'K2' => ['KEPEKAAN ISU', 'ANALISIS ISU', 'ISU'],
            'K3' => ['KREATIVITAS', 'DESAIN', 'FOTOGRAFI', 'KREATIF'],
        ];

        if (isset($aliasMap[$normalizedKode])) {
            foreach ($indicators as $ind) {
                if (in_array(strtoupper($ind), $aliasMap[$normalizedKode])) {
                    return true;
                }
            }
        }

        if ($this->kriteria_kode && strtoupper($this->kriteria_kode) === $normalizedKode) {
            return true;
        }

        return false;
    }

    public function getIndikatorLabelsAttribute(): array
    {
        $labels = [];
        if ($this->hasIndikator('K1')) {
            $labels['K1'] = 'K1 Kepenulisan';
        }
        if ($this->hasIndikator('K2')) {
            $labels['K2'] = 'K2 Kepekaan Isu';
        }
        if ($this->hasIndikator('K3')) {
            $labels['K3'] = 'K3 Kreativitas';
        }
        return $labels;
    }
}
