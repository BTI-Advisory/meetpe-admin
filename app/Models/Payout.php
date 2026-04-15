<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;
    protected $table="payouts";
    protected $primaryKey = 'id';
    protected $fillable = ["guide_id","stripe_transfer_id","invoice_url","amount","paid_at","payment_period"];

    public function guide()
    {
        return $this->belongsTo(Guide::class,"guide_id");
    } 
}
