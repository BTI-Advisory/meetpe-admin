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
        Schema::table('reservations', function (Blueprint $table) {
                    // Ajoute une colonne ENUM avec les nouvelles valeurs possibles
            $table->string('status')->enum(['En attente', 'Acceptée', 'Refusée', 'Annulée', 'Archivée', 'Crée', 'Abondonnée'])
                  ->default('Crée')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('status',100, ['En attente', 'Acceptée', 'Refusée','Annulée','Archivée'])->default('En attente')->change();
        });
    }
};
