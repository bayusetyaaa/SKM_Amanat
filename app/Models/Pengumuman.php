<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'target_audience',
        'isi',
        'lampiran_path',
        'lampiran_data',
        'lampiran_nama',
        'lampiran_mime',
        'penulis',
        'dibuat_oleh',
    ];

    protected static function booted()
    {
        static::addGlobalScope('excludeLampiranData', function ($builder) {
            $builder->select([
                'id',
                'judul',
                'target_audience',
                'isi',
                'lampiran_path',
                'lampiran_nama',
                'lampiran_mime',
                'penulis',
                'dibuat_oleh',
                'created_at',
                'updated_at',
            ]);
        });
    }

    public function scopeWithLampiranData($query)
    {
        return $query->withoutGlobalScope('excludeLampiranData');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
