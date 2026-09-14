<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilCalonAnggota extends Model
{
    use HasFactory;

    protected $table = 'profil_calon_anggota';

    protected $fillable = [
        'user_id',
        'nim',
        'prodi',
        'angkatan',
        'no_hp',
        'alamat',
        'pilihan_divisi_awal',
        'seleksi_administrasi',
        'tes_tulis_wawancara',
        'cakruma',
        'keputusan_final',
    ];

    /**
     * Hitung status kelulusan dinamis berdasarkan 3 tahapan seleksi
     */
    public function getStatusSeleksiAttribute(): string
    {
        if (
            $this->seleksi_administrasi === 'tidak_lolos' ||
            $this->tes_tulis_wawancara === 'tidak_lolos' ||
            $this->cakruma === 'tidak_lolos'
        ) {
            return 'tidak_lolos';
        }

        if (
            $this->seleksi_administrasi === 'lolos' &&
            $this->tes_tulis_wawancara === 'lolos' &&
            $this->cakruma === 'lolos'
        ) {
            return 'lolos';
        }

        return 'proses_seleksi';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
