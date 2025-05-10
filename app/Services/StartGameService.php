<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateGameDto;
use App\Events\CreateGame;
use App\Exceptions\HttpException;
use App\Models\Game;
use App\Parents\Service;
use Illuminate\Support\Facades\Redis;

final class StartGameService extends Service
{
    public function run(int $userId, string $roomId): void
    {
        $roomKey = "rooms:$roomId";
        $members = Redis::hgetall($roomKey);
        $this->validate($members, $userId);

        unset($members[$userId]);
        $uid2 = array_key_first($members);

        // Если второй игрок еще не нажал начать игру
        if(!$uid2 || !$members[$uid2])
        {
            Redis::hset($roomKey, $userId, 1);
            return;
        }

        // Если второй игрок нажал начать игру, создаем игру
        $game = $this->createGame($userId, $uid2);
        Redis::del($roomKey);

        CreateGame::broadcast($roomId, $game->id, $this->getFirstPlayerId($game));
    }

    private function validate(array $members, int $userId): void
    {
        if (empty($members)) {
            throw new HttpException(404, "Комната не найдена");
        }
        if (!isset($members[$userId])) {
            throw new HttpException(403, "Вы не находитесь в данной комнате");
        }
    }

    private function createGame(int $uid1, int $uid2): Game
    {
        return (new CreateGameService())->run(
            CreateGameDto::fromArray([
                'uid1' => $uid1,
                'uid2' => $uid2
            ])
        );
    }

    private function getFirstPlayerId(Game $game): int
    {
        return rand(0, 1) ? $game->uid1 : $game->uid2;
    }
}