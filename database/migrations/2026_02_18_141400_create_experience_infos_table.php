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
        Schema::create('experience_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_experience_id')
                ->constrained('guide_experiences')
                ->cascadeOnDelete();

            $table->enum('type', ['to_bring', 'good_to_know']);
            $table->text('content');
            
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_infos');
    }
};
