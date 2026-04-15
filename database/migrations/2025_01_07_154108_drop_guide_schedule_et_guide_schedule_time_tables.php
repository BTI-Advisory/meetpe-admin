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
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_schedule_times', function (Blueprint $table) {
            $table->dropForeign(['guide_schedule_id']);
        });
        Schema::dropIfExists('guide_schedules');
        Schema::dropIfExists('guide_schedule_time');
    }
};
