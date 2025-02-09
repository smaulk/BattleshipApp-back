<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateGameDto;
use App\Models\Game;
use App\Parents\Service;
use Throwable;

final class CreateGameService extends FriendshipService
{
    public function run(CreateGameDto $dto): Game
    {
        $this->checkIds($dto->uid1, $dto->uid2);

        $game = new Game();
        $game->uid1 = $dto->uid1;
        $game->uid2 = $dto->uid2;
        $game->status = $dto->status;
        $game->saveOrFail();

        return $game;
    }
}