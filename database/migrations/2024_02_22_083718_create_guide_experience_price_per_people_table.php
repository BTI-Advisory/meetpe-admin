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
        Schema::create('guide_experience_price_per_people', function (Blueprint $table) {
            $table->id();
            $table->integer("number_of_persons");
            $table->integer("price_per_person");
            $table->unsignedBigInteger("guide_experience_id");
            $table->foreign("guide_experience_id")->references("id")->on("guide_experiences");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_experience_price_per_people');
    }
};
