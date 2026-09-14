<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function profilTarget()
    {
        return $this->hasMany(ProfilTarget::class, 'divisi_id');
    }

    public function hasilProfileMatching()
    {
        return $this->hasMany(HasilProfileMatching::class, 'divisi_id');
    }
}
