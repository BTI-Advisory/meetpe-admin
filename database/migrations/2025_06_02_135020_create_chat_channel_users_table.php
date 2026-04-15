<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatChannelUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('chat_channel_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('channel_id')->constrained('chat_channels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('role'); // "guide" ou "voyageur"
            $table->boolean('is_admin')->default(false); // le guide est admin du groupe par défaut

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_channel_users');
    }
}
