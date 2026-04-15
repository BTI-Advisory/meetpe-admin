<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienceSchedules extends Model
{
    use HasFactory;
    protected $fillable = ["planning_id","start_time","end_time"];
    public function planning()
    {
        return $this->belongsTo(ExperiencePlannings::class,'planning_id');
    }
}
