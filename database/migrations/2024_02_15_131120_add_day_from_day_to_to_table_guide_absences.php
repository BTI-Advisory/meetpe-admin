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
        Schema::table('guide_absence_times', function (Blueprint $table) {
            //
            $table->time("from")->nullable()->change();
            $table->time("to")->nullable()->change();
            $table->date("date_from")->nullable();
            $table->date("date_to")->nullable();
            $table->date("day")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_absence_times', function (Blueprint $table) {
            //
            $table->dropColumn("date_from");
            $table->dropColumn("date_to");
            $table->time("from")->change();
            $table->time("to")->change();
            $table->date("day")->change();


        });
    }
};
