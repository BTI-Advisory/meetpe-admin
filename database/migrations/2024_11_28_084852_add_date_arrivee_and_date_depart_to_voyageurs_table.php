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
        Schema::table('voyageurs', function (Blueprint $table) {
            $table->date('date_depart')->nullable()->after('date_arrivee'); // Ajoute date_depart après date_arrivee
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyageurs', function (Blueprint $table) {
            //
            $table->dropColumn(['date_depart']); // Supprime les colonnes
        });
    }
};
