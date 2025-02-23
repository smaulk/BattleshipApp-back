<?php

namespace App\Events;

use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PrivateChannel;

final class CreateRoom extends BroadcastEvent
{
    public int $uid1;
    public int $uid2;
    public string $roomId;

    public function __construct(int $uid1, int $uid2, string $roomId)
    {
        $this->uid1 = $uid1;
        $this->uid2 = $uid2;
        $this->roomId = $roomId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("users.$this->uid1.events"),
            new PrivateChannel("users.$this->uid2.events"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'create.room';
    }

    public function broadcastWith(): array
    {
        return [
            'uid1'   => $this->uid1,
            'uid2'   => $this->uid2,
            'roomId' => $this->roomId
        ];
    }
}
