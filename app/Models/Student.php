<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik',
        'nisn',
        'name',
        'gender', // Tambahkan ini
        'grade',
        'admission_year',
        'dormitory',
        'photo_url'
    ];

    public function parent()
    {
        // Kembali ke relasi default
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    // Tambahkan di dalam class Student
    public function guidances()
    {
        return $this->hasMany(StudentGuidance::class);
    }
}