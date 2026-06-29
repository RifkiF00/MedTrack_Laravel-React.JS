<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'm_ruangan';
    protected $primaryKey = 'id_ruang';

    protected $fillable = [
        'nama_ruang',
        'kategori',
        'lokasi_gedung',
        'latitude',
        'longitude',
        'foto',
    ];

    /**
     * Relasi ke User
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_ruang', 'id_ruang');
    }

    /**
     * Relasi ke Aset (Plural)
     */
    public function asets()
    {
        return $this->hasMany(Aset::class, 'id_ruang_saat_ini', 'id_ruang');
    }

    /**
     * Relasi ke Aset (Singular, used in controllers)
     */
    public function aset()
    {
        return $this->hasMany(Aset::class, 'id_ruang_saat_ini', 'id_ruang');
    }
}
