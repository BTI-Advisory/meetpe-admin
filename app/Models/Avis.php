<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GuideExperience;

class Avis extends Model
{
    protected $fillable = ['user_id','experience_id', 'note','message'];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function experience()
    {
        return $this->belongsTo(GuideExperience::class, "experience_id");
    }

}
