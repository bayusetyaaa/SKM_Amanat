<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCalonAnggota(): bool
    {
        return $this->role === 'calon_anggota';
    }

    public function profil()
    {
        return $this->hasOne(ProfilCalonAnggota::class, 'user_id');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'user_id');
    }

    public function nilaiEvaluasi()
    {
        return $this->hasMany(NilaiEvaluasi::class, 'user_id');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'user_id');
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class, 'user_id');
    }

    public function hasilProfileMatching()
    {
        return $this->hasMany(HasilProfileMatching::class, 'user_id');
    }
}
