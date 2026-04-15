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
            // Supprimer la colonne guid_id
            // Supprimer la contrainte de clé étrangère
            $table->dropForeign(['guid_id']);
            $table->dropColumn('guid_id');
            $table->dropColumn('dure');

            // Ajouter les colonnes nom, prenom et phone
            $table->string('nom', 100)->after('voyageur_id');
            $table->string('prenom', 100)->after('nom');
            $table->string('phone', 20)->after('prenom');

            // Modifier la colonne status en un ENUM
            $table->string('status',100, ['En attente', 'Acceptée', 'Refusée','Annulée','Archivée'])->default('En attente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Réajouter la colonne guid_id
            $table->unsignedBigInteger('guid_id')->nullable()->after('message_au_guide');
            $table->foreign('guid_id')->references('id')->on('guides')->onDelete('cascade');

            $table->double('dure')->nullable()->after('guid_id');

            // Supprimer les colonnes nom, prenom et phone
            $table->dropColumn(['nom', 'prenom', 'phone']);

            // Revertir la colonne status en son état précédent
            $table->string('status')->change();
        });
    }
};
