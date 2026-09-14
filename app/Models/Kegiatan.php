<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $fillable = [
        'jenis',
        'nama',
        'tempat',
        'deskripsi',
        'tanggal_waktu',
        'token_presensi',
    ];

    protected static function booted()
    {
        static::creating(function ($kegiatan) {
            if (empty($kegiatan->token_presensi)) {
                $kegiatan->token_presensi = strtoupper(\Illuminate\Support\Str::random(6));
            }
        });

        static::saved(function ($kegiatan) {
            $evalService = app(\App\Services\EvaluasiNilaiService::class);
            $evalService->syncAllScores();

            $pmService = app(\App\Services\ProfileMatchingService::class);
            $pmService->calculateAndRankAll();
        });

        static::deleted(function ($kegiatan) {
            $evalService = app(\App\Services\EvaluasiNilaiService::class);
            $evalService->syncAllScores();

            $pmService = app(\App\Services\ProfileMatchingService::class);
            $pmService->calculateAndRankAll();
        });
    }

    protected $casts = [
        'tanggal_waktu' => 'datetime',
    ];

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'kegiatan_id');
    }
}
