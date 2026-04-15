<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = ["date_time","nombre_des_voyageurs","experience_id","voyageur_id","message_au_guide","nom","phone","is_payed","is_group","status",
    "total_price" ,
    "guide_payout_amount",
    "commission_meetpe",
    "guide_payout_percentage",
    "refund_amount",
    "canceled_at",
    "cancel_reason",
    "cancel_description",
    "stripe_payment_intent_id",
    "stripe_payment_error",
    "stripe_charge_id",
    "stripe_refund_id",
    "stripe_refund_status" // mettre le status de refund si c'est ok ou non
];
    protected $casts = [
        "is_payed"=>"boolean"
    ];
    public function voyageur(){
        return $this->belongsTo(User::class,"voyageur_id");
    }
    public function experience(){
        return $this->belongsTo(GuideExperience::class,"experience_id");
    }
    public function getStatusAttribute($value)
    {
        $locale = explode(',', request()->header('Accept-Language', 'fr'))[0];
        $locale = in_array($locale, ['fr','en']) ? $locale : 'fr';
        App::setLocale($locale);
        return __('choices.'.strval($value));
    }

}
