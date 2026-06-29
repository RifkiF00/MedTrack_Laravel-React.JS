<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    protected $table = 'm_aset';
    protected $primaryKey = 'id_aset';

    protected $fillable = [
        'kode_label',
        'nama_alat',
        'kategori_aset',
        'jumlah_unit',
        'merk',
        'model',
        'serial_number',
        'no_sertifikat',
        'tgl_pengadaan',
        'tgl_kalibrasi_terakhir',
        'tgl_kadaluarsa_sertif',
        'harga_perolehan',
        'status_kondisi',
        'id_ruang_saat_ini',
        'lokasi_fisik',
        'keterangan',
        'gambar_aset',
        'latitude',
        'longitude',
        'file_qr_code',
    ];

    protected $casts = [
        'tgl_pengadaan' => 'date',
        'tgl_kalibrasi_terakhir' => 'date',
        'tgl_kadaluarsa_sertif' => 'date',
        'harga_perolehan' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relasi ke Ruangan
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruang_saat_ini', 'id_ruang');
    }

    /**
     * Relasi ke Tracking Log
     */
    public function trackings()
    {
        return $this->hasMany(Tracking::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke Maintenance
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'id_aset', 'id_aset');
    }
}
