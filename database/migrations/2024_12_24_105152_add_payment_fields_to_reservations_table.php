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
            $table->decimal('total_price', 10, 2)->default(0)->after('status');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('total_price');
            $table->boolean('is_group')->default(false)->after('refund_amount');
        });
        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->renameColumn('max_number_of_persons', 'max_group_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['total_price', 'refund_amount']);
        });
        Schema::table('guide_experiences', function (Blueprint $table) {
            $table->renameColumn('max_group_size', 'max_number_of_persons');
        });
    }
};
