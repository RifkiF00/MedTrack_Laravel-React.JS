<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanLog extends Model
{
    use HasFactory;

    protected $table = 't_pemeliharaan_log';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_pemeliharaan',
        'id_user_pelaksana',
        'tgl_pelaksanaan',
        'tgl_rencana',
        'status_pelaksanaan',
        'hasil_pengecekan',
        'kondisi_laporan',
        'foto_dokumentasi',
        'catatan_khusus',
    ];

    protected $casts = [
        'tgl_pelaksanaan' => 'datetime',
        'tgl_rencana' => 'date',
    ];

    /**
     * Relasi ke Pemeliharaan (Master)
     */
    public function pemeliharaan()
    {
        return $this->belongsTo(Pemeliharaan::class, 'id_pemeliharaan', 'id_pemeliharaan');
    }

    /**
     * Relasi ke User Pelaksana
     */
    public function pelaksana()
    {
        return $this->belongsTo(User::class, 'id_user_pelaksana', 'id_user');
    }
}
