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
              // Drop the foreign key constraint
              $table->dropForeign(['guide_schedule_id']);
              // Drop the column itself
              $table->dropColumn('guide_schedule_id');
              $table->unsignedBigInteger("user_id");
              $table->foreign("user_id")->references("id")->on("users");
              $table->dateTime("from")->change();
              $table->dateTime("to")->change();
              $table->dropColumn("day");

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_absence_times', function (Blueprint $table) {
            //
            $table->unsignedBigInteger("guide_schedule_id");
            $table->foreign("guide_schedule_id")->references("id")->on("guide_schedules");
               // Drop the foreign key constraint
               $table->dropForeign(['user_id']);
               // Drop the column itself
               $table->dropColumn('user_id');
               $table->time("from")->change();
               $table->time("to")->change();
               $table->string("day");

        });
    }
};
