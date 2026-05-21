<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bounding box + status filter (requête matching principale)
        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->index(['status', 'lat', 'lang'], 'idx_ge_status_lat_lang');
        });

        // JOIN experience_plannings → experience_id
        Schema::table('experience_plannings', function (Blueprint $table) {
            $table->index('experience_id', 'idx_ep_experience_id');
        });

        // Filtre responses par entity + entity_id + question_id
        Schema::table('responses', function (Blueprint $table) {
            $table->index(['entity', 'entity_id', 'question_id'], 'idx_responses_entity_id_question');
        });

        // JOIN guide_absence_times → user_id
        Schema::table('guide_absence_times', function (Blueprint $table) {
            $table->index('user_id', 'idx_gat_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->dropIndex('idx_ge_status_lat_lang');
        });
        Schema::table('experience_plannings', function (Blueprint $table) {
            $table->dropIndex('idx_ep_experience_id');
        });
        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex('idx_responses_entity_id_question');
        });
        Schema::table('guide_absence_times', function (Blueprint $table) {
            $table->dropIndex('idx_gat_user_id');
        });
    }
};
