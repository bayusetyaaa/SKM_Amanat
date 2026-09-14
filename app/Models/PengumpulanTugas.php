<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'penugasan_id',
        'user_id',
        'nama_file',
        'file_data',
        'file_path',
        'mime_type',
        'ukuran_file',
        'status',
        'nilai',
        'feedback',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function ($pengumpulan) {
            if ($pengumpulan->user_id) {
                $user = $pengumpulan->user ?? User::find($pengumpulan->user_id);
                if ($user) {
                    $evalService = app(\App\Services\EvaluasiNilaiService::class);
                    $evalService->syncUserScores($user);

                    $pmService = app(\App\Services\ProfileMatchingService::class);
                    $pmService->calculateAndRankAll();
                }
            }
        });

        static::deleted(function ($pengumpulan) {
            if ($pengumpulan->user_id) {
                $user = $pengumpulan->user ?? User::find($pengumpulan->user_id);
                if ($user) {
                    $evalService = app(\App\Services\EvaluasiNilaiService::class);
                    $evalService->syncUserScores($user);

                    $pmService = app(\App\Services\ProfileMatchingService::class);
                    $pmService->calculateAndRankAll();
                }
            }
        });
    }

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
