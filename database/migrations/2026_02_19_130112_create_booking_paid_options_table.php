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
        Schema::create('booking_paid_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('reservations')
                ->cascadeOnDelete();

            $table->foreignId('experience_paid_option_id')
                ->constrained('experience_paid_options')
                ->cascadeOnDelete();

            // Snapshot du prix au moment de la réservation
            $table->decimal('price_per_person', 8, 2);

            // Nombre de personnes pour cette option
            $table->integer('quantity')->default(1);

            // Sous-total calculé
            $table->decimal('total_price', 10, 2);

            $table->timestamps();

            // Empêche la même option d’être ajoutée 2 fois pour le même booking
            $table->unique(['booking_id', 'experience_paid_option_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_paid_options');
    }
};
