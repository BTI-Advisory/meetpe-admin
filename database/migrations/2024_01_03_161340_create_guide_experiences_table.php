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
        Schema::create('guide_experiences', function (Blueprint $table) {
            $table->id();
            $table->string("categorie");
            $table->string("nom");
            $table->string("description");
            $table->string("dure");
            $table->string("prix_par_voyageur");
            $table->string("inclus");
            $table->string("nombre_des_voyageur");
            $table->string("type_des_voyageur");
            $table->string("ville");
            $table->string("addresse");
            $table->string("code_postale");
            //address
            //gallerie
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_experiences');
    }
};
