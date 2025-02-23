<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\EndGameDto;
use App\Events\EndGame;
use App\Exceptions\HttpException;
use App\Models\Game;
use App\Parents\Service;
use Illuminate\Support\Facades\Redis;

final class FinishGameService extends Service
{
    public function run(EndGameDto $dto): void
    {
        $game = Game::query()->findOrFail($dto->gameId);

        if(!in_array($dto->userId, [$game->uid1, $game->uid2], true)) {
            throw new HttpException(403, "Вы не можете завершить данную игру");
        }

        $status = $dto->type->toStatus($game->uid1 === $dto->userId);
        $key = "games:$dto->gameId";
        $data = Redis::hgetall($key);

        if (empty($data) || (count($data) === 1 && isset($data[$dto->userId]))) {
            Redis::hset($key, $dto->userId, $status->value);
            Redis::expire($key, 120);
            return;
        }

        unset($data[$dto->userId]);
        if($status->value != reset($data)){
            EndGame::broadcast($game->id, false);
            throw new HttpException(400, "Ошибка проверки результатов игры");
        }

        $game->status = $status;
        $game->saveOrFail();

        Redis::del($key);
        EndGame::broadcast($game->id, true);
    }
}