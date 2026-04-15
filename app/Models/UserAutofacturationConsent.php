<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAutofacturationConsent extends Model
{
    use HasFactory;
    protected $fillable= ["text_version","accepted_at","platform","ip_address","app_version","user_id"];
    protected $casts = [
        'accepted_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,"user_id");
    }
}
