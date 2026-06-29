<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 't_maintenance';
    protected $primaryKey = 'id_main';

    protected $fillable = [
        'id_aset',
        'id_user_teknisi',
        'id_vendor',
        'jenis_tindakan',
        'tgl_mulai',
        'tgl_selesai',
        'deskripsi_kendala',
        'tindakan_diambil',
        'biaya',
        'status_perbaikan',
        'file_laporan',
    ];

    protected $casts = [
        'tgl_mulai' => 'datetime',
        'tgl_selesai' => 'datetime',
        'biaya' => 'decimal:2',
    ];

    /**
     * Relasi ke Aset
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke User Teknisi
     */
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'id_user_teknisi', 'id_user');
    }

    /**
     * Relasi ke Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }
}
