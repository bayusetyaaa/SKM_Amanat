<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilProfileMatching extends Model
{
    use HasFactory;

    protected $table = 'hasil_profile_matching';

    protected $fillable = [
        'user_id',
        'divisi_id',
        'ncf',
        'nsf',
        'nilai_total',
        'ranking',
        'rekomendasi',
    ];

    protected $casts = [
        'ncf' => 'float',
        'nsf' => 'float',
        'nilai_total' => 'float',
        'rekomendasi' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }
}
