<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceCondition extends Model
{
    protected $fillable = [
        'guide_experience_id',
        'difficulty',
        'pmr_accessible',
        'equipment_included',
        'outfit_required',
        'meal_included',
    ];

    public function experience()
    {
        return $this->belongsTo(GuideExperience::class, 'guide_experience_id');
    }
}
