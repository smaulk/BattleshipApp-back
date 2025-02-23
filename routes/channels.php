<?php

use App\Classes\WebSocket\Channels\GameChannel;
use App\Classes\WebSocket\Channels\OnlineChannel;
use App\Classes\WebSocket\Channels\RoomChannel;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{userId}.online', OnlineChannel::class);

Broadcast::channel('users.{userId}.events', function (User $user, int $userId): bool {
    return $user->id === $userId;
});

Broadcast::channel('rooms.{roomId}',RoomChannel::class);
Broadcast::channel('games.{gameId}', GameChannel::class);