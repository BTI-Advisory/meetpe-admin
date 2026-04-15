<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDEvice extends Model
{
    use HasFactory;


    protected $fillable = [
        "deviceBrand",
        "deviceModel",
        "deviceOsVersion",
        "appVersion",
        "user_id"
    ];

}
