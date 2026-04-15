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

        Schema::table('guides', function (Blueprint $table) {

            $table->string("pro_local");
            $table->dropColumn("truc_a_toi");
            $table->dropColumn("phone_number");
            $table->dropColumn("languages");
            $table->dropColumn("personnalite");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
