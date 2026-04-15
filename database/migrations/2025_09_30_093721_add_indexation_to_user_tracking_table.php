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
        Schema::table('user_trackings', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
            $table->index('route');
            $table->index(['user_id', 'created_at']); // utile si tu filtres par user sur une période
            $table->index('ip_address'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_trackings', function (Blueprint $table) {
            //
        });
    }
};
