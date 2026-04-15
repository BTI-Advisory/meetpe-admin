<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AddSeederClassName extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Data for each table
        $experience_fr = [
            ['lable' => 'Culture Locale', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Tradition et Savoir-Faire', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Food', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Art', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Patrimoine', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Sport', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Activités Extrêmes', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Musique et Concert', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Outdoor', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Insolites', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Exclusives', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Shopping', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Avec les animaux', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Exploration Urbaine', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Vin & Spiritueux', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Pas d’idée, fais moi découvrir', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
        ];

        $guide_personalite_fr = [
            ['lable' => 'Extraverti', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Curieux', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Créatif', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Rêveur', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Sportif', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Connecté', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Epicurien', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Posé', 'description' => '', 'created_at' => now(), 'updated_at' => now()]
        ];

        $voyage_preference_fr = [
            ['lable' => 'A la montagne', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'En bord de mer', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Dans un petit village', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'En ville', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'A la campagne', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Dans un hotel All Inclusive', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'à l’aise partout', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
        ];

        $voyage_type_fr = [
            ['lable' => 'Solo', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'En famille', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Avec des potes', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'En couple', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
        ];

        $voyage_mode_fr = [
            ['lable' => 'Aventurier', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'La Culture avant tout', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Gastronome', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Ecotouriste', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Fétard', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Sportif', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Un peu de tout', 'description' => '', 'created_at' => now(), 'updated_at' => now()],


        ];

        $voyage_personalite_fr = [
            ['lable' => 'Extraverti', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Curieux', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Créatif', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Rêveur', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Sportif', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Connecté', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Epicurien', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Posé', 'description' => '', 'created_at' => now(), 'updated_at' => now()]
        ];

        $guide_truc_de_toi_fr = [
            ['lable' => 'L’aventure', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'La Gastronomie', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'La Culture', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'L’écotourisme', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'La fête', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
            ['lable' => 'Curieux de tout', 'description' => '', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Repeat for other tables...

        // Insert data into tables

        DB::table('guide_experience_fr')->insert($experience_fr);
        DB::table('guide_personalite_fr')->insert($guide_personalite_fr);
        DB::table('voyage_preference_fr')->insert($voyage_preference_fr);
        DB::table('voyage_type_fr')->insert($voyage_type_fr);
        DB::table('voyage_mode_fr')->insert($voyage_mode_fr);
        DB::table('voyage_personalite_fr')->insert($voyage_personalite_fr);
        DB::table('guide_truc_de_toi_fr')->insert($guide_truc_de_toi_fr);

    }

}
