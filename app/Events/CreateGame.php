<?php

namespace App\Events;

use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PresenceChannel;

final class CreateGame extends BroadcastEvent
{
    public string $roomId;
    public int $gameId;
    public int $firstPlayerId;

    public function __construct(string $roomId, int $gameId, int $firstPlayerId)
    {
        $this->roomId = $roomId;
        $this->gameId = $gameId;
        $this->firstPlayerId = $firstPlayerId;
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
            'gameId'        => $this->gameId,
            'firstPlayerId' => $this->firstPlayerId,
        ];
    }
}
