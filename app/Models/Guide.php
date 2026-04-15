<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Enums\ReservationStatus;

class Guide extends Model
{
    use HasFactory;
    protected $table="guides";
    protected $primaryKey = 'guide_id';
    protected $fillable = ["pro_local","user_id","personalite_fr","guide_truc_de_toi_fr","stripe_account_id","stripe_connect_form_url","stripe_connect_form_status"];

    public function user()
    {
        return $this->belongsTo(User::class,"user_id");
    } 
    public function failedPayouts()
    {
        return $this->hasMany(FailedPayout::class, 'guide_id');
    }
    public function hasPayoutInProgress()
    {
        


        //check if guide have failed payout
        $guidePayouts =  $this->failedPayouts()->first();
        if($guidePayouts)
            return true;


        //check if he has archived or accepted reservations for the current month
        $debutMoisDernier = Carbon::now()->startOfMonth();
        $finMoisDernier = Carbon::now()->endOfMonth();

        $thisMonthReservations = Reservation::join('guide_experiences', 'reservations.experience_id', '=', 'guide_experiences.id')
        ->selectRaw('guide_experiences.user_id as guide_id')
        ->where('guide_experiences.user_id', $this->user_id) // ✅ filtrer par le guide actuel
        ->whereBetween('reservations.date_time', [$debutMoisDernier, $finMoisDernier])
        ->where('reservations.guide_payout_amount', '!=', 0)
        ->exists();

        if($thisMonthReservations)
        {
            return true;
        }

        return false;
    }
}
