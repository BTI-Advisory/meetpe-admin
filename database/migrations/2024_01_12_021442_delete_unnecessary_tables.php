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
        //
        Schema::dropIfExists('voyageur_experiences');
        Schema::dropIfExists('voyageur_languages_fr');
        Schema::dropIfExists('voyageur_rencontre_fr');
        Schema::dropIfExists('voyage_mode_fr');
        Schema::dropIfExists('voyage_personalite_fr');
        Schema::dropIfExists('voyage_preference_fr');
        Schema::dropIfExists('voyage_type_fr');
        Schema::dropIfExists('voyage_voyageurs');
        // -- guides table
        Schema::dropIfExists('guide_experience_fr');
        Schema::dropIfExists('guide_language_fr');
        Schema::dropIfExists('guide_personalite_fr');
        Schema::dropIfExists('guide_truc_de_toi_fr');
        Schema::dropIfExists('db_check_sums');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
