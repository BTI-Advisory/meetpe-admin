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
        Schema::create('voyage_voyageurs', function (Blueprint $table) {
            $table->id("voyage_voyageur_id");
            $table->unsignedBigInteger("voyage_id");
            $table->foreign("voyage_id")->references("voyage_id")->on("voyages");
            $table->unsignedBigInteger("voyageur_id");
            $table->foreign("voyageur_id")->references("voyageur_id")->on("voyageurs");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voyage_voyageurs');
    }
};
