<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;

    protected $table = 'berkas';

    protected $fillable = [
        'user_id',
        'jenis_berkas',
        'nama_file',
        'file_data',
        'file_path',
        'mime_type',
        'ukuran_file',
        'status',
        'catatan',
    ];

    protected $hidden = [
        'file_data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
