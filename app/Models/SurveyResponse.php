<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $fillable = ['survey_id', 'user_id'];

    public function survey() {
        return $this->belongsTo(Survey::class);
    }

    public function user() {
        return $this->belongsTo(User::class); // Relasi ke Orang Tua
    }

    public function answers() {
        return $this->hasMany(SurveyAnswer::class);
    }
}