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
        Schema::create('game_invitations', function (Blueprint $table) {
            $table->foreignId('sender_id')
                ->index()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('receiver_id')
                ->index()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamp('invited_at');

            $table->primary(['sender_id', 'receiver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_invitations');
    }
};
