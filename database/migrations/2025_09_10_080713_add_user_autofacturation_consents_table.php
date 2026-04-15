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
        Schema::create('user_autofacturation_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('text_version'); // version du texte accepté
            $table->timestamp('accepted_at'); // horodatage
            $table->string('ip_address')->nullable();
            $table->string('app_version')->nullable();
            $table->string('platform')->nullable(); // web, ios, android
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_autofacturation_consents');
    }
};
