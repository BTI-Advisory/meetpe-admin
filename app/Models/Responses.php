<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Responses extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "choice_id", "question_id", "entity", "entity_id"];

    public function questionChoice()
    {
        return $this->belongsTo(QuestionChoice::class, 'choice_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public static function getchoicesOf($responseIds)
    {
        return DB::table('responses')
            ->join('question_choices', 'responses.choice_id', '=', 'question_choices.id')
            ->whereIn('responses.id', $responseIds)
            ->select('responses.id AS id', 'question_choices.choice_txt AS choix', 'question_choices.svg As svg')
            ->get();
    }
}
