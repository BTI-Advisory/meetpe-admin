<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $table = "guides";
    protected $primaryKey = 'guide_id';
    protected $fillable = [
        "pro_local", "user_id",
        "personalite_fr", "guide_truc_de_toi_fr",
        "rhythm", "accompaniment_style", "what_resonates_most", "how_do_you_meet_people",
        "stripe_account_id", "stripe_connect_form_url", "stripe_connect_form_status",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class, 'guide_id');
    }

    public function failedPayouts()
    {
        return $this->hasMany(FailedPayout::class, 'guide_id');
    }

    public function hasPayoutInProgress(): bool
    {
        if ($this->failedPayouts()->exists()) return true;

        return Reservation::join('guide_experiences', 'reservations.experience_id', '=', 'guide_experiences.id')
            ->where('guide_experiences.user_id', $this->user_id)
            ->whereBetween('reservations.date_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->where('reservations.guide_payout_amount', '!=', 0)
            ->exists();
    }
}
