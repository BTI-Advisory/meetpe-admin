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
        Schema::table('responses', function (Blueprint $table) {
            $table->enum('entity', ['voyageur', 'guide', 'experience'])
                  ->after('user_id')->nullable()
                  ->comment('Entité concernée par la réponse');

            $table->unsignedBigInteger('entity_id')
                  ->after('entity')->nullable()
                  ->comment('ID de l’entité concernée');
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->index(['entity', 'entity_id'], 'idx_entity_entity_id');
           /* $table->unique(
                ['entity', 'entity_id', 'question_id', 'choice_id'],
                'uniq_response_entity_question_choice'
            );*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex('idx_entity_entity_id');
            $table->dropColumn(['entity', 'entity_id']);
        });
    }
};
