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
            $table->string('ville')->nullable()->after('rencontre'); // Adresse (ville)
            $table->string('pays')->nullable()->after('ville'); // Adresse (pays)
            $table->date('date_arrivee')->nullable()->after('pays'); // Date souhaitée
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyageurs', function (Blueprint $table) {
            //
            $table->dropColumn(['ville', 'pays', 'date_arrivee']);
        });
    }
};
