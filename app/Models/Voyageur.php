<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voyageur extends Model
{
    use HasFactory;
    protected $primaryKey = 'voyageur_id';
    protected $fillable = 
    [
        "user_id",
        "mode",
        "type",
        "preference",
        "personnalite",
        "experiences",
        "languages",
        "deplacement",
        "rencontre",
        "ville",
        "pays",
        "date_arrivee",
        "date_depart",
        "lat",
        "lang"
    ];

    public function user()
    {
        return $this->belongsTo(User::class,"user_id");
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class,'voyageur_id','user_id');
    }
}
