<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSettings extends Model
{
    use HasFactory;
    protected $fillable = [
        "reservation_email",
        "reservation_app",
        "reservation_sms",
        "reservation_appel_telephone",
        "notification_meetpe_email",
        "notification_meetpe_app",
        "notification_meetpe_sms",
        "notification_meetpe_appel_telephone",
        "user_id"
    ];
     protected $casts = [
        "reservation_email"=>"boolean",
        "reservation_app"=>"boolean",
        "reservation_sms"=>"boolean",
        "reservation_appel_telephone"=>"boolean",
        "notification_meetpe_email"=>"boolean",
        "notification_meetpe_app"=>"boolean",
        "notification_meetpe_sms"=>"boolean",
        "notification_meetpe_appel_telephone"=>"boolean",
    ];
}
