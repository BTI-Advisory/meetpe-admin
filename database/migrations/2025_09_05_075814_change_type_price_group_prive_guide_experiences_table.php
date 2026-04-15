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
            // On modifie le type du champ
            $table->decimal('price_group_prive', 10, 2)->change();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {
            // On remet en int si rollback
            $table->integer('price_group_prive')->change();
        });
    }
};
