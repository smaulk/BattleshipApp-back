<?php

use App\Enums\FriendshipStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uid1')
                ->index()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('uid2')
                ->index()
                ->constrained('users')
                ->cascadeOnDelete();;
            $table->enum('status', FriendshipStatus::names())
                ->index();
            $table->timestamps();

            $table->unique(['uid1', 'uid2']);
        });

        // Проверка, что uid1 < uid2
        DB::statement('ALTER TABLE friendships ADD CONSTRAINT check_uid1_less_than_uid2 CHECK (uid1 < uid2)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
