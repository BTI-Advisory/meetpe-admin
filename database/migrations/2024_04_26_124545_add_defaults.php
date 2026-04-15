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
            $table->string("inclus")->default("non inclus")->nullable()->change();
            $table->integer("nombre_des_voyageur")->default(4)->nullable()->change();
            $table->string("type_des_voyageur")->default("non")->nullable()->change();
            $table->integer("price_group_prive")->default(0)->nullable()->change();
            $table->string("dernier_minute_reservation")->nullable()->change();
            $table->string("audio_file")->default("non")->nullable()->change();
            $table->boolean("discount_kids_between_2_and_12")->default(false)->change();
            $table->integer("max_number_of_persons")->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {
            //
        });
    }
};
