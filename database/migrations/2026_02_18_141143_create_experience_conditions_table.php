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
        Schema::create('experience_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_experience_id')
                ->constrained('guide_experiences')
                ->cascadeOnDelete();

            $table->tinyInteger('difficulty')->nullable(); // 1,2,3
            $table->boolean('pmr_accessible')->default(false);
            $table->boolean('equipment_included')->default(false);
            $table->boolean('outfit_required')->default(false);
            $table->boolean('meal_included')->default(false);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_conditions');
    }
};
