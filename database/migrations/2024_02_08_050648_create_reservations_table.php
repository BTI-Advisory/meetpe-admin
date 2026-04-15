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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->dateTime("date_time");
            $table->integer("nombre_des_voyageurs");
            $table->string("message_au_guide");
            $table->boolean("is_payed");
            $table->unsignedBigInteger("voyageur_id");
            $table->foreign("voyageur_id")->references("id")->on("users");
            $table->unsignedBigInteger("guid_id");
            $table->foreign("guid_id")->references("id")->on("users");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
