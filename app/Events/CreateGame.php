<?php

namespace App\Events;

use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PrivateChannel;

final class CreateGame extends BroadcastEvent
{
    public int $gameId;
    public string $roomId;

    public function __construct(int $gameId, string $roomId)
    {
        $this->gameId = $gameId;
        $this->roomId = $roomId;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("rooms.$this->roomId");
    }

    public function broadcastAs(): string
    {
        return 'game.create';
    }

    public function broadcastWith(): array
    {
        return [
            'gameId' => $this->gameId,
        ];
    }
}
