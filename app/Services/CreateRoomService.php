<?php
declare(strict_types=1);

namespace App\Services;

use App\Parents\Service;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

final class CreateRoomService extends Service
{
    public function run(int $uid1, ?int $uid2 = null): string
    {
        $roomId = Str::ulid()->toString();
        $roomKey = "rooms:$roomId";

        $members = array_merge([$uid1, 0], ($uid2 ? [$uid2, 0] : []));
        Redis::hset($roomKey, ...$members);
        Redis::expire($roomKey, $this->getRoomTtl());

        return $roomId;
    }

    private function getRoomTtl(): int
    {
        return (int)config('gameplay.room.ttl');
    }
}