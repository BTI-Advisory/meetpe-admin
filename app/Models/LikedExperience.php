<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikedExperience extends Model
{
    use HasFactory;
    protected $fillable = ["experience_id","user_id","matching_percentage"];

    public function experience()
    {
        return $this->belongsTo(GuideExperience::class,"experience_id");
    }
}
