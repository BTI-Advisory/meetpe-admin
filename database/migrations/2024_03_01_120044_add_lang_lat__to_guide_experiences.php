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
            $table->string("lang")->nullable();
            $table->string("lat")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {

            $table->dropColumn("lang");
            $table->string("lat");
        });
    }
};
