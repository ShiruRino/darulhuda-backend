<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGuidance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'type', 'date', 'title', 
        'description', 'points', 'handled_by'
    ];

    // Relasi balik ke model Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}