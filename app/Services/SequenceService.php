<?php

namespace App\Services;

use App\Models\QuestionChoice;
use App\Models\Responses;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SequenceService
{

    public static function GenerateSequence(int $id)
    {

        $user_choices = DB::select("
                SELECT
                    question_choices.id,
                    question_choices.choice_txt,
                    CASE WHEN responses.choice_id IS NOT NULL THEN 1 ELSE 0 END AS response_selected
                FROM
                    question_choices
                LEFT JOIN
                    responses ON question_choices.id = responses.choice_id AND responses.user_id = ?
                where choice_key = 'personalite_fr' or choice_key = 'languages_fr'
                ;
            ", [$id]);
        $user_choices_values = [];
        $user_data_choices = [];
        foreach ($user_choices as $choice) {
            if($choice->response_selected == 1){
                $user_data_choices[] = $choice->choice_txt;
            }
            //echo $choice->id;
//            $user_choices_values[] = [
//                "id"=>$choice->id,
//                "response_selected"=>$choice->response_selected,
//                "choice_txt"=>$choice->choice_txt
//            ];
            $user_choices_values[] = $choice->response_selected;
        }
        return [$user_choices_values,$user_data_choices];
    }
//    public static function GetMatchedAnswers(array $data) :array{
//        $matches = [];
//        foreach ($data as $d){
//            if($d["response_selected"] == 1){
//                array_push($matches,$d);
//            }
//        }
//        return $matches;
//    }

   public static function GetUserChoices(int $guide_id)
   {
        return DB::select("SELECT
            question_choices.choice_txt

        FROM
            question_choices
         JOIN
            responses ON question_choices.id = responses.choice_id AND responses.user_id = ?
        ",[$guide_id]);
   }
}
