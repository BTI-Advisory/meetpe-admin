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
            $table->string('rhythm')->nullable();
            $table->string('accompaniment_style')->nullable()->after('rhythm');
            $table->string('what_resonates_most')->nullable()->after('accompaniment_style');
            $table->string('how_do_you_meet_people')->nullable()->after('what_resonates_most');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->dropColumn([
                'rhythm',
                'accompaniment_style',
                'what_resonates_most',
                'how_do_you_meet_people',
            ]);
        });
    }
};
