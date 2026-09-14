<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilTarget extends Model
{
    use HasFactory;

    protected $table = 'profil_target';

    protected $fillable = [
        'divisi_id',
        'kriteria_id',
        'nilai_target',
        'faktor',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }
}
