<?php
declare(strict_types=1);

namespace App\Events;

use App\Dto\SendNotifyDto;
use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PrivateChannel;

final class SendNotify extends BroadcastEvent
{
    public int $senderId;
    public int $receiverId;
    public string $event;
    public ?string $message;

    public function __construct(SendNotifyDto $dto)
    {
        $this->senderId = $dto->senderId;
        $this->receiverId = $dto->receiverId;
        $this->event = $dto->event;
        $this->message = $dto->message;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("users.$this->receiverId.events");
    }

    public function broadcastAs(): string
    {
        return $this->event;
    }

    public function broadcastWith(): array
    {
        return [
            'message'  => $this->message,
            'senderId' => $this->senderId,
        ];
    }
}