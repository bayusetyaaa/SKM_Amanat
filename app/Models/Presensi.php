<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'user_id',
        'kegiatan_id',
        'waktu_hadir',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'waktu_hadir' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function ($presensi) {
            if ($presensi->user_id) {
                $user = $presensi->user ?? User::find($presensi->user_id);
                if ($user) {
                    $evalService = app(\App\Services\EvaluasiNilaiService::class);
                    $evalService->syncUserScores($user);

                    $pmService = app(\App\Services\ProfileMatchingService::class);
                    $pmService->calculateAndRankAll();
                }
            }
        });

        static::deleted(function ($presensi) {
            if ($presensi->user_id) {
                $user = $presensi->user ?? User::find($presensi->user_id);
                if ($user) {
                    $evalService = app(\App\Services\EvaluasiNilaiService::class);
                    $evalService->syncUserScores($user);

                    $pmService = app(\App\Services\ProfileMatchingService::class);
                    $pmService->calculateAndRankAll();
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}
