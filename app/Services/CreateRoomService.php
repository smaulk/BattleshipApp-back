<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\GameInvitation;
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

        $this->deleteSendInvitations($uid1, $uid2);

        return $roomId;
    }

    private function getRoomTtl(): int
    {
        return (int)config('gameplay.room.ttl');
    }

    /**
     * Удаляем все отправленные приглашения пользователей
     */
    private function deleteSendInvitations(int $uid1, ?int $uid2): void
    {
        GameInvitation::query()
            ->where('sender_id', $uid1)
            ->when($uid2, function ($query)  use ($uid2) {
                $query->orWhere('sender_id', $uid2);
            })
            ->delete();
    }
}