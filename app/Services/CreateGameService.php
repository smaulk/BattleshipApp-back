<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateGameDto;
use App\Enums\GameStatus;
use App\Models\Game;
use App\Services\Abstract\UsersService;
use Throwable;

final class CreateGameService extends UsersService
{
    /**
     * @throws Throwable
     */
    public function run(CreateGameDto $dto): Game
    {
        $this->validateUsers($dto->uid1, $dto->uid2);

        $game = new Game();
        $game->uid1 = $dto->uid1;
        $game->uid2 = $dto->uid2;
        $game->status = $dto->status ?? GameStatus::CREATED;
        $game->saveOrFail();

        return $game;
    }
}