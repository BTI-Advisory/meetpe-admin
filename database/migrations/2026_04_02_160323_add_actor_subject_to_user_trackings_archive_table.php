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
        Schema::table('user_trackings_archive', function (Blueprint $table) {
            $table->string('actor_type')->nullable()->after('user_id');
            $table->string('subject_type')->nullable()->after('actor_type');
        });
    }

    public function down(): void
    {
        Schema::table('user_trackings_archive', function (Blueprint $table) {
            $table->dropColumn(['actor_type', 'subject_type']);
        });
    }
};
