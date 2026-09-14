<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';

    protected $fillable = [
        'kode',
        'nama',
        'parameter',
        'sumber_penilaian',
        'aspek',
        'deskripsi',
    ];

    public function profilTarget()
    {
        return $this->hasMany(ProfilTarget::class, 'kriteria_id');
    }

    public function nilaiEvaluasi()
    {
        return $this->hasMany(NilaiEvaluasi::class, 'kriteria_id');
    }
}
