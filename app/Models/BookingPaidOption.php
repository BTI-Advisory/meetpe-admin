<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class BookingPaidOption extends Model
{
    protected $fillable = [
        'booking_id',
        'experience_paid_option_id',
        'price_per_person',
        'quantity',
        'total_price',
    ];

    protected $appends = ['option_title', 'option_description'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'booking_id');
    }

    public function option()
    {
        return $this->belongsTo(ExperiencePaidOption::class, 'experience_paid_option_id');
    }

    public function getOptionTitleAttribute(): ?string
    {
        $option = $this->relationLoaded('option') ? $this->option : null;
        if (!$option) return null;

        return App::getLocale() === 'en' && $option->title_en
            ? $option->title_en
            : $option->title;
    }

    public function getOptionDescriptionAttribute(): ?string
    {
        $option = $this->relationLoaded('option') ? $this->option : null;
        if (!$option) return null;

        return App::getLocale() === 'en' && $option->description_en
            ? $option->description_en
            : $option->description;
    }
}
