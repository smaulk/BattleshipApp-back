<?php

namespace Database\Seeders;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    private array $statuses;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->statuses = GameStatus::names();
        $count = User::query()->count();

        // Создаем случайные связи 1 пользователя с остальными
        for ($uid2 = 2; $uid2 < $count; $uid2++) {
            $this->createGame(1, $uid2);
        }

        // Создаем случайные связи между пользователями
        for ($i = 0; $i < $count * 2; $i++) {
            $uid1 = rand(2, $count);
            $uid2 = rand(2, $count);
            if ($uid1 === $uid2) {
                continue;
            }

            $this->createGame($uid1, $uid2);
        }
    }

    private function createGame(int $uid1, int $uid2): void
    {
        Game::create([
            'uid1'       => $uid1,
            'uid2'       => $uid2,
            'status'     => $this->statuses[array_rand($this->statuses)],
        ]);
    }
}
