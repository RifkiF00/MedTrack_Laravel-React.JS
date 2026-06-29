<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    use HasFactory;

    protected $table = 'm_pemeliharaan';
    protected $primaryKey = 'id_pemeliharaan';

    protected $fillable = [
        'nama_item',
        'deskripsi',
        'lokasi',
        'frekuensi',
        'pic_penanggung_jawab',
        'catatan',
        'status',
    ];

    /**
     * Relasi ke Log Pemeliharaan
     */
    public function logs()
    {
        return $this->hasMany(PemeliharaanLog::class, 'id_pemeliharaan', 'id_pemeliharaan');
    }
}
