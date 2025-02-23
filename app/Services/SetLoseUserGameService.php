<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Parents\Service;

final class SetLoseUserGameService extends Service
{
    /**
     * Если игра не завершена, делаем игрока проигравшим
     */
    public function run(int $gameId, int $userId): void
    {
        $game = Game::query()
            ->where('id', $gameId)
            ->where('status', GameStatus::CREATED)
            ->first();

        if (!$game) {
            return;
        }

        // Делаем пользователя проигравшим
        $game->status = $userId === $game->uid1
            ? GameStatus::WIN_UID2
            : GameStatus::WIN_UID1;

        $game->save();
    }
}