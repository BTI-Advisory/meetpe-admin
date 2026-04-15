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
            $table->string('title_en')->nullable()->after('title');
            $table->string('description_en', 3000)->nullable(false)->after('description');
            $table->string('ville_en', 255)->after('ville');
            $table->string('country_en', 255)->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'description_en', 'ville_en', 'country_en']);
        });
    }
};
