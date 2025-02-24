<?php
declare(strict_types=1);

namespace App\Classes\WebSocket\Channels;

use App\Models\User;
use App\Parents\Channel;
use Illuminate\Support\Facades\Redis;

class RoomChannel extends Channel
{
    public function join(User $user, string $roomId): bool
    {
        $roomKey = "rooms:$roomId";
        $members = Redis::hkeys($roomKey);
        if(empty($members) || !in_array($user->id, $members)) {
            return false;
        }

        return true;
    }
}