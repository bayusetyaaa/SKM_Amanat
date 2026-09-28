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
        'tanggal_waktu_selesai',
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
        'tanggal_waktu_selesai' => 'datetime',
    ];

    public function getWaktuFormattedAttribute(): string
    {
        $start = $this->tanggal_waktu;
        $end = $this->tanggal_waktu_selesai;

        if (!$start) return '-';

        if (!$end) {
            return $start->translatedFormat('l, d F Y - H:i') . ' WIB';
        }

        if ($start->isSameDay($end)) {
            return $start->translatedFormat('l, d F Y') . ' (' . $start->format('H:i') . ' - ' . $end->format('H:i') . ' WIB)';
        }

        return $start->translatedFormat('d M Y, H:i') . ' s/d ' . $end->translatedFormat('d M Y, H:i') . ' WIB';
    }

    public function isPresensiOpen(): bool
    {
        $now = \Carbon\Carbon::now();
        if ($this->tanggal_waktu && $now->lt($this->tanggal_waktu)) {
            return false;
        }
        if ($this->tanggal_waktu_selesai && $now->gt($this->tanggal_waktu_selesai)) {
            return false;
        }
        return true;
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'kegiatan_id');
    }
}
