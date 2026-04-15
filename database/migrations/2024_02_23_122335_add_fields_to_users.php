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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string("IBAN")->nullable();
            $table->string("BIC")->nullable();
            $table->string("nom_du_titulaire")->nullable();
            $table->string("rue")->nullable();
            $table->string("code_postal")->nullable();
            $table->string("ville")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn("IBAN");
            $table->dropColumn("BIC");
            $table->dropColumn("nom_du_titulaire");
            $table->dropColumn("rue");
            $table->dropColumn("code_postal");
            $table->dropColumn("ville");
        });
    }
};
