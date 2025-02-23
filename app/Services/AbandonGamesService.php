<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Parents\Service;

/**
 * Помечает игры как ABANDONED, если они не завершились в течение установленного времени
 */
final class AbandonGamesService extends Service
{
    public function __invoke(): void
    {
        Game::query()
            ->where('status', GameStatus::CREATED)
            ->where('created_at', '<', now()->subSeconds($this->getGameTtl()))
            ->update([
                'status' => GameStatus::ABANDONED,
                'ended_at' => now(),
            ]);
    }

    private function getGameTtl(): int
    {
        return (int)config('gameplay.game.ttl');
    }
}