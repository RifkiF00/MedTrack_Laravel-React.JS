<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TroubleshootLog extends Model
{
    use HasFactory;

    protected $table = 't_troubleshoot_log';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_ticket',
        'status_lama',
        'status_baru',
        'catatan',
        'diubah_oleh',
    ];

    /**
     * Relasi ke Ticket Troubleshoot
     */
    public function ticket()
    {
        return $this->belongsTo(Troubleshoot::class, 'id_ticket', 'id_ticket');
    }

    /**
     * Relasi ke User pengubah status
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'diubah_oleh', 'id_user');
    }
}
