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
        Schema::table('question_choices', function (Blueprint $table) {
            $table
                ->unsignedSmallInteger('order_index')
                ->nullable()
                ->after('choice_key')
                ->comment('Ordre d’affichage métier des choix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_choices', function (Blueprint $table) {
            $table->dropColumn('order_index');
        });
    }
};
