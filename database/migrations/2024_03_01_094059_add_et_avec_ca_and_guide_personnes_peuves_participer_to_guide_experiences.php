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
        Schema::table('guide_experiences', function (Blueprint $table) {
            //
            $table->string("guide_personnes_peuves_participer")->nullable();
            $table->string("et_avec_ça")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {
            //
            $table->dropColumn("guide_personnes_peuves_participer");
            $table->dropColumn("et_avec_ça");
        });
    }
};
