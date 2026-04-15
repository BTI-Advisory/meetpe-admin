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
        Schema::table('reservations', function (Blueprint $table) {
            //
            $table->decimal("dure")->nullable()->change();
            $table->boolean("is_payed")->default(false)->change();
            $table->string("status")->default("Pending")->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            //
            $table->decimal("dure")->nullable(false)->change();
            $table->boolean("is_payed")->change(); // Assuming you're reverting to default true
            $table->string("status")->change(); // Assuming you're reverting to default empty string

        });
    }
};
