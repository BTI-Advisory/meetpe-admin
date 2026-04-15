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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guide_id'); // ID du guide
            $table->decimal('amount', 10, 2); // Montant payé
            $table->string('stripe_transfer_id'); // ID du transfert Stripe
            $table->timestamp('paid_at'); // Date du paiement
            $table->timestamps();
        
            $table->foreign('guide_id')->references('guide_id')->on('guides')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
