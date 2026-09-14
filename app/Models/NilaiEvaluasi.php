<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiEvaluasi extends Model
{
    use HasFactory;

    protected $table = 'nilai_evaluasi';

    protected $fillable = [
        'user_id',
        'kriteria_id',
        'nilai_aktual',
        'input_oleh',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    public function inputOleh()
    {
        return $this->belongsTo(User::class, 'input_oleh');
    }
}
