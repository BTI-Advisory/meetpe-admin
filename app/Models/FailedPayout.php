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
}
