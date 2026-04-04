<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Pastikan ini ada untuk Sanctum

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'nik',
        'relationship', // Tambahkan ini
        'password',
        'role',
        'fcm_token',
    ];

    public function students()
    {
        // Kembali ke relasi default menggunakan user_id
        return $this->hasMany(Student::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke Kritik & Saran
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}