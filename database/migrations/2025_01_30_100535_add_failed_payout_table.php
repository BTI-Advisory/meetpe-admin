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
        Schema::create('failed_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payout_id')->unique()->nullable();
            $table->unsignedBigInteger('guide_id'); // ID du guide
            $table->string('stripe_account_id');  
            $table->decimal('payout_amount', 10, 2); // 10 chiffres max, dont 2 après la virgule  
            $table->text('failure_message')->nullable();
            $table->enum('status', ['failed', 'success'])->default('failed');
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
