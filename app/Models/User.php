<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'm_user';
    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'nama_lengkap',
        'role',
        'id_ruang',
        'nip',
        'no_hp',
        'alamat',
        'status',
        'last_login',
        'kontak_darurat_1',
        'kontak_darurat_2',
        'kontak_darurat_3',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_login' => 'datetime',
    ];

    /**
     * Override password attribute for Laravel Auth.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    protected $appends = ['profile_photo_url'];

    /**
     * Get user's profile photo URL or null
     */
    public function getProfilePhotoUrlAttribute()
    {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $upload_dir = public_path('uploads/profiles');
        
        foreach ($allowed_ext as $ext) {
            $file = 'profile_' . $this->id_user . '.' . $ext;
            if (file_exists($upload_dir . DIRECTORY_SEPARATOR . $file)) {
                return asset('uploads/profiles/' . $file) . '?t=' . filemtime($upload_dir . DIRECTORY_SEPARATOR . $file);
            }
        }
        
        return null;
    }

    /**
     * Relasi ke Ruangan
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruang', 'id_ruang');
    }
}
