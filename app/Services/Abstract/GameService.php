<?php
declare(strict_types=1);

namespace App\Services\Abstract;

use App\Dto\SendMoveResultDto;
use App\Exceptions\HttpException;
use App\Models\Game;
use App\Parents\Service;

class GameService extends Service
{
    public function getGame(int $gameId): Game
    {
        return Game::query()->findOrFail($gameId);
    }

    public function validate(int $userId, Game $game): void
    {
        if (!in_array($userId, [$game->uid1, $game->uid2], true)) {
            throw new HttpException(403, 'Вы не участвуете в данной игре');
        }
    }
}