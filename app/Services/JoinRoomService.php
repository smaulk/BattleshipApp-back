<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;
use App\Parents\Service;
use Illuminate\Support\Facades\Redis;

final class JoinRoomService extends Service
{
    public function run(string $roomId, int $userId): void
    {
        $roomKey = "rooms:$roomId";
        $members = Redis::hkeys($roomKey);

        if ($this->validate($members, $userId)) {
            Redis::hset($roomKey, $userId, 0);
        }
    }

    private function validate(array $members, int $userId): bool
    {
        if (empty($members)) {
            throw new HttpException(404, "Комната не найдена");
        }
        if (in_array($userId, $members)) {
            return false;
        }
        if (count($members) > 1) {
            throw new HttpException(403, "Комната уже заполнена");
        }

        return true;
    }
}