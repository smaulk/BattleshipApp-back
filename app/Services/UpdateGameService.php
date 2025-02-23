<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Parents\Service;

final class UpdateGameService extends Service
{
    public function run(int $gameId, GameStatus $status): bool
    {
        return (bool)Game::query()
            ->where('id', $gameId)
            ->update(['status' => $status]);
    }
}