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
        Schema::create('guide_absence_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("guide_schedule_id");
            $table->foreign("guide_schedule_id")->references("id")->on("guide_schedules");
            $table->string("day");
            $table->time("from");
            $table->time("to");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_absence_times');
    }
};
