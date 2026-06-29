<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    protected $table = 't_tracking';
    protected $primaryKey = 'id_track';

    protected $fillable = [
        'id_aset',
        'id_user',
        'id_ruang',
        'latitude',
        'longitude',
        'akurasi_gps',
        'tgl_update',
        'keterangan',
    ];

    protected $casts = [
        'tgl_update' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relasi ke Aset
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke User (Staf pencatat scan)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Ruangan
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruang', 'id_ruang');
    }
}
