<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('questions')->where('question_key', 'languages_fr')
            ->update(['contexts' => 'voyageur,experience']);

        DB::table('questions')->where('question_key', 'voyage_type_fr')
            ->update(['contexts' => 'voyageur,experience']);

        DB::table('questions')->where('question_key', 'voyageur_tu_te_deplace_comment')
            ->update(['contexts' => 'voyageur']);

        DB::table('questions')->where('question_key', 'voyageur_experiences')
            ->update(['contexts' => 'voyageur,experience']);

        DB::table('questions')->where('question_key', 'rhythm')
            ->update(['contexts' => 'guide,voyageur']);

        DB::table('questions')->where('question_key', 'voyageur_vivre_experience')
            ->update(['contexts' => 'voyageur']);

        DB::table('questions')->where('question_key', 'voyageur_attraction_decouverte')
            ->update(['contexts' => 'voyageur']);

        DB::table('questions')->where('question_key', 'how_do_you_meet_people')
            ->update(['contexts' => 'guide,voyageur']);

        DB::table('questions')->where('question_key', 'what_resonates_most')
            ->update(['contexts' => 'guide']);

        DB::table('questions')->where('question_key', 'accompaniment_style')
            ->update(['contexts' => 'guide']);

        DB::table('questions')->where('question_key', 'reservation_de_dernier_minute')
            ->update(['contexts' => 'experience']);
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('questions')->update(['contexts' => null]);
    }
};
