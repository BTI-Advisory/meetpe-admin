<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperiencePaidOption extends Model
{
    protected $fillable = [
        'guide_experience_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'price_per_person',
        'is_available',
    ];

    public function experience()
    {
        return $this->belongsTo(GuideExperience::class, 'guide_experience_id');
    }

    public function bookingOptions()
    {
        return $this->hasMany(BookingPaidOption::class, 'experience_paid_option_id');
    }
}
