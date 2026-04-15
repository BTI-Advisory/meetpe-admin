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
        Schema::table('guide_experiences', function (Blueprint $table) {
                $table->decimal('prix_a_paye_par_voyageur', 10, 2)->nullable()->after('prix_par_voyageur');
                $table->decimal('prix_groupe_prive_paye_par_voyageur', 10, 2)->nullable()->after('price_group_prive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->dropColumn('prix_a_paye_par_voyageur');
            $table->dropColumn('prix_groupe_prive_paye_par_voyageur');
        });
    }
};
