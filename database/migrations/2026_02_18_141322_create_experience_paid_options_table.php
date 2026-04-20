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
        Schema::create('experience_paid_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_experience_id')
                ->constrained('guide_experiences')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price_per_person', 8, 2);
            $table->boolean('is_available')->default(true);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_paid_options');
    }
};
