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
            //

              $table->string("categorie")->nullable()->change();
                $table->string("nom")->nullable()->change();
                 // Utiliser 'renameColumn' pour renommer la colonne
                $table->string('title')->nullable()->after('nom'); // Créez la nouvelle colonne
                $table->dropColumn('nom'); // Supprimez l'ancienne colonne
               // ancienne version mariadb
               // $table->renameColumn("nom","title");

                $table->string("description")->nullable()->change();
                $table->string("dure")->nullable()->change();
                $table->string("prix_par_voyageur")->nullable()->change();
                $table->string("inclus")->nullable()->change();
                $table->string("nombre_des_voyageur")->nullable()->change();
                $table->string("type_des_voyageur")->nullable()->change();
                $table->string("ville")->nullable()->change();
                $table->string("addresse")->nullable()->change();
                $table->string("code_postale")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {
            //
            $table->string('nom')->after('title'); // Créez la colonne originale
            $table->dropColumn('title'); // Supprimez la nouvelle colonne
            $table->string("categorie")->change();
            $table->string("title")->change();
            $table->string("description")->change();
            $table->string("dure")->change();
            $table->string("prix_par_voyageur")->change();
            $table->string("inclus");
            $table->string("nombre_des_voyageur")->change();
            $table->string("type_des_voyageur")->change();
            $table->string("ville")->change();
            $table->string("addresse")->change();
            $table->string("code_postale")->change();

        });
    }
};
