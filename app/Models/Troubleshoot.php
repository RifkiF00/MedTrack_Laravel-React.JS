<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Troubleshoot extends Model
{
    use HasFactory;

    protected $table = 't_troubleshoot';
    protected $primaryKey = 'id_ticket';
    public $timestamps = false;

    protected $fillable = [
        'id_aset',
        'id_user_pelapor',
        'nama_pelapor_bebas',
        'id_teknisi_penanggungjawab',
        'tgl_lapor',
        'tingkat_urgensi',
        'deskripsi_kerusakan',
        'foto_kerusakan',
        'status_ticket',
    ];

    protected $casts = [
        'tgl_lapor' => 'datetime',
    ];

    /**
     * Relasi ke Aset
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke User Pelapor
     */
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_user_pelapor', 'id_user');
    }

    /**
     * Relasi ke User Teknisi PJ
     */
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'id_teknisi_penanggungjawab', 'id_user');
    }

    /**
     * Relasi ke Log Ticket
     */
    public function logs()
    {
        return $this->hasMany(TroubleshootLog::class, 'id_ticket', 'id_ticket');
    }
}
