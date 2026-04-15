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
        Schema::create('guid_experience_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("guide_experience_id");
            $table->foreign("guide_experience_id")->references("id")->on("guide_experiences");
            $table->string("photo_url");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guid_experience_photos');
    }
};
