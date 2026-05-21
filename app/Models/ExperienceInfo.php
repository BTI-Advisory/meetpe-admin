<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceInfo extends Model
{
    protected $fillable = [
        'guide_experience_id',
        'type',
        'content',
        'content_en',
    ];

    public function experience()
    {
        return $this->belongsTo(GuideExperience::class, 'guide_experience_id');
    }
}
