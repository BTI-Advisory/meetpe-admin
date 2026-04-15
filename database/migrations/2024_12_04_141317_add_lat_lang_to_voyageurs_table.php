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
            //
            $table->string('lat')->nullable()->after('pays'); // Remplace 'column_name' par la colonne après laquelle tu veux ajouter
            $table->string('lang')->nullable()->after('lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyageurs', function (Blueprint $table) {
            //
        });
    }
};
