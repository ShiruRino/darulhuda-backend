<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['title', 'description', 'end_date', 'is_active'];

    public function questions() {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function responses() {
        return $this->hasMany(SurveyResponse::class);
    }
}