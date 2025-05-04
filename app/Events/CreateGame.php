<?php

namespace App\Events;

use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PresenceChannel;

final class CreateGame extends BroadcastEvent
{
    public int $gameId;
    public string $roomId;

    public function __construct(int $gameId, string $roomId)
    {
        $this->gameId = $gameId;
        $this->roomId = $roomId;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel("rooms.$this->roomId");
    }

    public function broadcastAs(): string
    {
        return 'create.game';
    }

    public function broadcastWith(): array
    {
        return [
            'gameId' => $this->gameId,
        ];
    }
}
