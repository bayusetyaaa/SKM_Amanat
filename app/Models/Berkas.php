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

    protected static function booted()
    {
        static::addGlobalScope('excludeFileData', function ($builder) {
            $builder->select([
                'id',
                'user_id',
                'jenis_berkas',
                'nama_file',
                'file_path',
                'mime_type',
                'ukuran_file',
                'status',
                'catatan',
                'created_at',
                'updated_at',
            ]);
        });
    }

    public function scopeWithFileData($query)
    {
        return $query->withoutGlobalScope('excludeFileData');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
