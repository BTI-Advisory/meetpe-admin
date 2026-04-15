<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuideAbsenceTime extends Model
{
    use HasFactory;
    protected $fillable = ["day","user_id","from","to","date_from","date_to"];
}
