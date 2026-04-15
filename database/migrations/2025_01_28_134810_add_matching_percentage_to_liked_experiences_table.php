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
        Schema::table('liked_experiences', function (Blueprint $table) {
            $table->integer('matching_percentage') // Permet un nombre avec jusqu'à 3 chiffres avant la virgule et 2 après
            ->nullable()->after('experience_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('liked_experiences', function (Blueprint $table) {
            $table->dropColumn('matching_percentage');
        });
    }
};
