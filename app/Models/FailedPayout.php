<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedPayout extends Model
{
    use HasFactory;
    protected $table="failed_payouts";
    protected $primaryKey = 'id';
    protected $fillable = ["payout_id","stripe_account_id","guide_id","failure_message","status","month"];

    public function payout()
    {
        return $this->belongsTo(Payout::class, "payout_id");
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class, "guide_id");
    }
}
