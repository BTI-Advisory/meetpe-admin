<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajout d'index sur experience_plannings (start_date)
        Schema::table('experience_plannings', function (Blueprint $table) {
            $table->index('start_date'); // Index simple
        });

        // Ajout d'index sur experience_schedules (start_time)
        Schema::table('experience_schedules', function (Blueprint $table) {
            $table->index('start_time'); // Index simple
        });

        // Ajout d'index composite sur reservations (experience_id et date_time)
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['experience_id', 'date_time']); // Index composite
        });

        // Ajout d'index sur guide_experiences (user_id)
        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->index('user_id'); // Index simple
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression des index
        Schema::table('experience_plannings', function (Blueprint $table) {
            $table->dropIndex(['start_date']);
        });

        Schema::table('experience_schedules', function (Blueprint $table) {
            $table->dropIndex(['start_time']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['experience_id', 'date_time']);
        });

        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
}
