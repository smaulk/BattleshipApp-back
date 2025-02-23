<?php

use App\Enums\GameStatus;
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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uid1')
                ->index()
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('uid2')
                ->index()
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('status', GameStatus::names())->default(GameStatus::CREATED->name);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
