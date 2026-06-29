<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sirkulasi extends Model
{
    use HasFactory;

    protected $table = 't_sirkulasi';
    protected $primaryKey = 'id_pinjam';

    protected $fillable = [
        'id_aset',
        'id_user_peminjam',
        'ruang_asal',
        'ruang_tujuan',
        'tgl_pinjam',
        'tgl_kembali_rencana',
        'tgl_kembali_aktual',
        'status_pinjam',
        'keperluan',
        'kondisi_awal',
        'kondisi_akhir',
        'catatan',
    ];

    protected $casts = [
        'tgl_pinjam' => 'datetime',
        'tgl_kembali_rencana' => 'date',
        'tgl_kembali_aktual' => 'datetime',
    ];

    /**
     * Relasi ke Aset
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke User Peminjam
     */
    public function peminjam()
    {
        return $this->belongsTo(User::class, 'id_user_peminjam', 'id_user');
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
}
