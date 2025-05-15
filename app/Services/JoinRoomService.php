<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;
use App\Parents\Service;
use Illuminate\Support\Facades\Redis;

final class JoinRoomService extends Service
{
    public function run(string $roomId, int $userId): int
    {
        $roomKey = "rooms:$roomId";
        $members = Redis::hkeys($roomKey);
        $this->validate($members, $userId);
        Redis::hset($roomKey, $userId, 0);

        return $this->getRoomCurrentTtl($roomKey);
    }

    private function validate(array $members, int $userId): void
    {
        if (empty($members)) {
            throw new HttpException(404, "Комната не найдена");
        }
        if (in_array($userId, $members)) {
            return;
        }
        if (count($members) > 1) {
            throw new HttpException(403, "Комната уже заполнена");
        }
    }

    private function getRoomCurrentTtl(string $roomKey): int
    {
        $ttl = Redis::ttl($roomKey);
        if ($ttl < 0) {
            throw new HttpException(404, "Комната не найдена");
        }

        return $ttl;
    }
}