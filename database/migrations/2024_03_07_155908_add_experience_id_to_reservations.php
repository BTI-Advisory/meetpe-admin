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
        Schema::table('reservations', function (Blueprint $table) {
            //
            $table->unsignedBigInteger("experience_id")->nullable();
            $table->foreign("experience_id")->references("id")->on("guide_experiences");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            //
            $table->dropForeign(['experience_id']); // Drop foreign key constraint
            $table->dropColumn('experience_id'); // Drop the column
        });
    }
};
