<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = ['survey_id', 'question_text', 'type', 'is_required'];

    public function survey() {
        return $this->belongsTo(Survey::class);
    }
}