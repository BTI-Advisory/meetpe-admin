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
            $table->decimal('prix_par_voyageur', 10, 2)->change(); // 10 chiffres max, dont 2 après la virgule
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->text('prix_par_voyageur')->change(); // Revenir à TEXT si nécessaire
        });
    }
};
