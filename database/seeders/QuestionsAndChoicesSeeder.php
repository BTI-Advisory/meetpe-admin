<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionChoice;

class QuestionsAndChoicesSeeder extends Seeder
{
    public function run()
    {
        // Questions à insérer
        $question = Question::where('question_key','guide_personnes_peuves_participer')->first();
        if($question)
        {
            $choiceToAdd = ["choice_txt"=>"Les enfants",
                            "question_id"=>$question->id,
                            "choice_key"=>"guide_personnes_peuves_participer",
                            "svg"=>"enfants.png",
                            "created_at"=>NOW(),
                            "updated_at"=>NOW()
            ];
            QuestionChoice::insert($choiceToAdd);
        }
    }
}
