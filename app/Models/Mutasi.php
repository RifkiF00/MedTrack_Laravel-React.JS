<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    use HasFactory;

    protected $table = 't_mutasi';
    protected $primaryKey = 'id_mutasi';

    protected $fillable = [
        'id_aset',
        'ruang_asal',
        'ruang_tujuan',
        'id_user_pencatat',
        'tgl_mutasi',
        'alasan_mutasi',
        'status_mutasi',
        'catatan',
    ];

    protected $casts = [
        'tgl_mutasi' => 'datetime',
    ];

    /**
     * Relasi ke Aset
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke Ruang Asal
     */
    public function ruangAsal()
    {
        return $this->belongsTo(Ruangan::class, 'ruang_asal', 'id_ruang');
    }

    /**
     * Relasi ke Ruang Tujuan
     */
    public function ruangTujuan()
    {
        return $this->belongsTo(Ruangan::class, 'ruang_tujuan', 'id_ruang');
    }

    /**
     * Relasi ke User Pencatat
     */
    public function pencatat()
    {
        return $this->belongsTo(User::class, 'id_user_pencatat', 'id_user');
    }
}
