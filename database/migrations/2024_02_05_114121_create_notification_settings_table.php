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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean("reservation_email");
            $table->boolean("reservation_app");
            $table->boolean("reservation_sms");
            $table->boolean("reservation_appel_telephone");
            $table->boolean("notification_meetpe_email");
            $table->boolean("notification_meetpe_app");
            $table->boolean("notification_meetpe_sms");
            $table->boolean("notification_meetpe_appel_telephone");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
