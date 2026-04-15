<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Responses extends Model
{
    use HasFactory;
    protected $fillable = ["user_id","choice_id"];

    public static function getchoicesOf($responseIds)
        {
            // Récupérer les catégories avec leurs noms correspondants
            $choices = DB::table('responses')
                ->join('question_choices', 'responses.choice_id', '=', 'question_choices.id')
                ->whereIn('responses.id', $responseIds)
                ->select('responses.id AS id', 'question_choices.choice_txt AS choix','question_choices.svg As svg')
                ->get();
            return $choices;
        }   
}
