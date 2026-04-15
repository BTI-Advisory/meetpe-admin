<?php
namespace App\Services;

use App\Models\NotificationSettings;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService {

    public function InitAccount(User $user){

        $this->InitNotifications($user);
        
    }
    public function InitNotifications(User $user) : void{
        NotificationSettings::updateOrCreate([
            "reservation_email" => true,
            "reservation_app" => false,
            "reservation_sms" => false,
            "reservation_appel_telephone" => false,
            "notification_meetpe_email" => true,
            "notification_meetpe_app" => false,
            "notification_meetpe_sms" => false,
            "notification_meetpe_appel_telephone" => false,
            "user_id" => $user->id
        ]);
    }
   
}
