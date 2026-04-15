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
            $table->renameColumn('type_de_voyageur', 'mode');
            $table->renameColumn('style_de_voyage_prefere', 'type');
            $table->renameColumn('environnement_prefere', 'preference');
            $table->renameColumn('type_d_experience', 'experiences');
            $table->renameColumn('type_rencentre', 'rencontre');
            $table->renameColumn('personnalite', 'personnalite'); // reste inchangé
            $table->renameColumn('languages', 'languages');     // reste inchangé

            // Modifier tous les champs pour les rendre nullable
            $table->string('mode')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->string('preference')->nullable()->change();
            $table->string('experiences')->nullable()->change();
            $table->string('rencontre')->nullable()->change();
            $table->string('personnalite')->nullable()->change();
            $table->string('languages')->nullable()->change();

            
            // Ajouter le nouveau champ 'deplacement'
            $table->string('deplacement')->nullable()->after('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyageurs', function (Blueprint $table) {
            // Inverser les changements des noms de colonnes
            $table->renameColumn('mode', 'type_de_voyageur');
            $table->renameColumn('type', 'style_de_voyage_prefere');
            $table->renameColumn('preference', 'environnement_prefere');
            $table->renameColumn('experiences', 'type_d_experience');
            $table->renameColumn('rencontre', 'type_rencentre');
            // Supprimer le champ 'deplacement'
            $table->dropColumn('deplacement');
        });
    }
};
