<?php

namespace Database\Seeders;

use App\Enums\FriendshipStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendshipSeeder extends Seeder
{

    private array $statuses;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->statuses = FriendshipStatus::names();

        $count = User::query()->count();

        // Создаем случайные связи 1 пользователя с остальными
        for ($uid2 = 2; $uid2 < $count; $uid2++) {
            $this->createFriendship(1, $uid2);
        }

        // Создаем случайные связи между пользователями
        for ($i = 0; $i < $count * 2; $i++) {
            $uid1 = rand(2, $count);
            $uid2 = rand(2, $count);
            if ($uid1 === $uid2) {
                continue;
            }

            $this->createFriendship($uid1, $uid2);
        }
    }

    /**
     * Создание записи случайной дружеской связи между пользователями
     */
    private function createFriendship(int $uid1, int $uid2): void
    {
        [$minId, $maxId] = sort_nums($uid1, $uid2);

        if (
            DB::table('friendships')
                ->where('uid1', $minId)
                ->where('uid2', $maxId)
                ->exists()
        ) {
            return;
        }

        DB::table('friendships')->insert([
            'uid1'       => $minId,
            'uid2'       => $maxId,
            'status'     => $this->statuses[array_rand($this->statuses)],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
