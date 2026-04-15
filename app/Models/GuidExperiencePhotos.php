<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidExperiencePhotos extends Model
{
    use HasFactory;
    protected $fillable = ["guide_experience_id","photo_url","type_image"];

}
