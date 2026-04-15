<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DefaultValuesBuilder
{
    public static function LoadQuestions() : void {
        $formattedDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $questions =  [
            // Voyageur Questions
            [
                "question_text"=>"Tu es un voyageur plutôt…",
                "question_key"=>"voyage_mode_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            [
                "question_text"=>"Tu aimes voyager...",
                "question_key"=>"voyage_type_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            [
                "question_text"=>"Tu prefères être...",
                "question_key"=>"voyage_preference_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            [
                "question_text"=>"On dit de toi que tu es...",
                "question_key"=>"voyage_personalite_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            [
                "question_text"=>"Tu cherches des expériences...",
                "question_key"=>"voyageur_experiences",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            [
                "question_text"=>"Tu parles...",
                "question_key"=>"voyageur_languages_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            [
                "question_text"=>"Tu veux rencontrer...",
                "question_key"=>"voyageur_rencontre_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            // Guide Questions
            [
                "question_text"=>"Ton truc à toi c’est…",
                "question_key"=>"guide_truc_de_toi_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            [
                "question_text"=>"On dit de toi que tu es...",
                "question_key"=>"guide_personalite_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ],
            [
                "question_text"=>"Tu parles...",
                "question_key"=>"guide_language_fr",
                'created_at' => $formattedDateTime, 'updated_at' => $formattedDateTime
            ]
        ];
        DB::table("questions")->insert($questions);

    }
    public static function LoadDefaultAnswers(): void{
        $formattedDateTime = Carbon::now()->format('Y-m-d H:i:s');

        $choices_questions_list = [
            // 1
            // voyage_mode_fr
            [
                "question_id"=>1,
                "choice_txt" => "Aventurier",
                "created_at" => "2023-12-21 08:03:51",
                "updated_at" => "2023-12-21 08:03:51",
                "choice_key"=>"voyage_mode_fr"
            ],
            [
                "question_id"=>1,
                "choice_txt" => "La Culture avant tout",
                "created_at" => "2023-12-21 08:03:51",
                "updated_at" => "2023-12-21 08:03:51",
                "choice_key"=>"voyage_mode_fr"
            ],
            [
                "question_id"=>1,
                "choice_txt" => "Gastronome",
                "created_at" => "2023-12-21 08:03:51",
                "updated_at" => "2023-12-21 08:03:51",
                "choice_key"=>"voyage_mode_fr"
            ],
            [
                "question_id"=>1,
                "choice_txt" => "Ecotouriste",
                "created_at" => "2023-12-21 08:03:51",
                "updated_at" => "2023-12-21 08:03:51",
                "choice_key"=>"voyage_mode_fr"
            ],
            [
                "question_id"=>1,
                "choice_txt" => "Fétard",
                "created_at" => "2023-12-21 08:03:51",
                "updated_at" => "2023-12-21 08:03:51",
                "choice_key"=>"voyage_mode_fr"
            ],
            [
                "question_id"=>1,
                "choice_txt" => "Sportif",
                "created_at" => "2023-12-21 08:03:51",
                "updated_at" => "2023-12-21 08:03:51",
                "choice_key"=>"voyage_mode_fr"
            ],
            [
                "question_id"=>1,
                "choice_txt" => "Un peu de tout",
                "created_at" => "2023-12-21 08:03:51",
                "updated_at" => "2023-12-21 08:03:51",
                "choice_key"=>"voyage_mode_fr"
            ],
            // 2
            // voyage_type_fr
            [
                "question_id"=>2,
                "choice_txt" => "Solo",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_type_fr"
            ],    [
                "question_id"=>2,
                "choice_txt" => "En famille",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_type_fr"
            ],    [
                "question_id"=>2,
                "choice_txt" => "Avec des potes",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_type_fr"
            ],    [
                "question_id"=>2,
                "choice_txt" => "En couple",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_type_fr"
            ],
            // 3
            // voyage_preference_fr
            [
                "question_id"=>3,
                "choice_txt" => "A la montagne",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_preference_fr"
            ],  [
                "question_id"=>3,
                "choice_txt" => "En bord de mer",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_preference_fr"
            ],  [
                "question_id"=>3,
                "choice_txt" => "Dans un petit village",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_preference_fr"
            ],  [
                "question_id"=>3,
                "choice_txt" => "En ville",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_preference_fr"
            ],  [
                "question_id"=>3,
                "choice_txt" => "A la campagne",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_preference_fr"
            ],  [
                "question_id"=>3,
                "choice_txt" => "Dans un hotel All Inclusive",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_preference_fr"
            ],  [
                "question_id"=>3,
                "choice_txt" => "à l’aise partout",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyage_preference_fr"
            ],
            // 4
            // personalite_fr
            [
                "question_id"=>4,
                "choice_txt" => "Extraverti",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"personalite_fr"
            ], [
                "question_id"=>4,
                "choice_txt" => "Curieux",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"personalite_fr"
            ], [
                "question_id"=>4,
                "choice_txt" => "Créatif",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"personalite_fr"
            ], [
                "question_id"=>4,
                "choice_txt" => "Rêveur",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"personalite_fr"
            ], [
                "question_id"=>4,
                "choice_txt" => "Sportif",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"personalite_fr"
            ], [
                "question_id"=>4,
                "choice_txt" => "Connecté",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"personalite_fr"
            ], [
                "question_id"=>4,
                "choice_txt" => "Epicurien",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"personalite_fr"
            ], [
                "question_id"=>4,
                "choice_txt" => "Posé",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"personalite_fr"
            ],
            // 5
            // voyageur_experiences
            [
                "question_id"=>5,
                "choice_txt" => "Culture Locale",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],
            [
                "question_id"=>5,
                "choice_txt" => "Tradition et Savoir-Faire",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Food",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Art",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Patrimoine",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Sport",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Activités Extrêmes",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Musique et Concert",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Outdoor",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Insolites",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],  [
                "question_id"=>5,
                "choice_txt" => "Exclusives",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],
            [
                "question_id"=>5,
                "choice_txt" => "Shopping",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],    [
                "question_id"=>5,
                "choice_txt" => "Avec les animaux",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],
            [
                "question_id"=>5,
                "choice_txt" => "Exploration Urbaine",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ], [
                "question_id"=>5,
                "choice_txt" => "Vin & Spiritueux",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ], [
                "question_id"=>5,
                "choice_txt" => "Pas d’idée, fais moi découvrir",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_experiences"
            ],
            // 6
            // languages_fr
            [
                "question_id"=>6,
                "choice_txt" => "Français",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Anglais",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Chinois (mandarin)",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Japonais",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Espagnol",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Portugais",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Italien",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Grec",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Russe",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],     [
                "question_id"=>6,
                "choice_txt" => "Allemand",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"languages_fr"
            ],
            // 7
            // voyageur_rencontre_fr
            [
                "question_id"=>7,
                "choice_txt" => "Des Guides Professionnels",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_rencontre_fr"
            ], [
                "question_id"=>7,
                "choice_txt" => "Des Locaux Passionnés",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"voyageur_rencontre_fr"
            ],
            // 8
            // guide_truc_de_toi_fr
            [
                "question_id"=>8,
                "choice_txt" => "L’aventure",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"guide_truc_de_toi_fr"
            ],   [
                "question_id"=>8,
                "choice_txt" => "La Gastronomie",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"guide_truc_de_toi_fr"
            ],   [
                "question_id"=>8,
                "choice_txt" => "Curieux de tout",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"guide_truc_de_toi_fr"
            ],   [
                "question_id"=>8,
                "choice_txt" => "La Culture",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"guide_truc_de_toi_fr"
            ],   [
                "question_id"=>8,
                "choice_txt" => "L’écotourisme",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"guide_truc_de_toi_fr"
            ],   [
                "question_id"=>8,
                "choice_txt" => "La fête",
                "created_at" => $formattedDateTime,
                "updated_at" => $formattedDateTime,
                "choice_key"=>"guide_truc_de_toi_fr"
            ],
            // 9
            // guide_personalite_fr
//            [
//                "question_id"=>9,
//                "choice_txt" => "Extraverti",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_personalite_fr"
//            ],  [
//                "question_id"=>9,
//                "choice_txt" => "Curieux",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_personalite_fr"
//            ],  [
//                "question_id"=>9,
//                "choice_txt" => "Créatif",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_personalite_fr"
//            ],  [
//                "question_id"=>9,
//                "choice_txt" => "Rêveur",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_personalite_fr"
//            ],  [
//                "question_id"=>9,
//                "choice_txt" => "Sportif",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_personalite_fr"
//            ],  [
//                "question_id"=>9,
//                "choice_txt" => "Connecté",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_personalite_fr"
//            ],  [
//                "question_id"=>9,
//                "choice_txt" => "Epicurien",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_personalite_fr"
//            ],  [
//                "question_id"=>9,
//                "choice_txt" => "Posé",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_personalite_fr"
//            ],
//            // 10
//            // guide_language_fr
//            [
//                "question_id"=>10,
//                "choice_txt" => "Allemand",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Anglais",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Chinois (mandarin)",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Espagnol",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Français",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Grec",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Italien",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Japonais",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Portugais",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],  [
//                "question_id"=>10,
//                "choice_txt" => "Russe",
//                "created_at" => $formattedDateTime,
//                "updated_at" => $formattedDateTime,
//                "choice_key"=>"guide_language_fr"
//            ],
        ];
        DB::table("question_choices")->insert($choices_questions_list);
    }

}
