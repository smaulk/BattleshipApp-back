<?php

namespace App\Events;

use App\Models\User;
use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PrivateChannel;

final class InviteGame extends BroadcastEvent
{
    public int $senderId;
    public string $senderNickname;
    public int $receiverId;

    public function __construct(User $sender, int $receiverId)
    {
        $this->senderId = $sender->id;
        $this->senderNickname = $sender->nickname;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("users.$this->receiverId.events");
    }

    public function broadcastAs(): string
    {
        return 'get.invite';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "{$this->senderNickname} приглашает вас в игру!",
            'senderId' => $this->senderId,
        ];
    }
}
