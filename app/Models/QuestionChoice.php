<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionChoice extends Model
{
    use HasFactory;

    protected $fillable = ["question_id", "choice_txt", "description", "choice_key", "svg", "order_index"];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
