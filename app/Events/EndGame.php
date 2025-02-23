<?php
declare(strict_types=1);

namespace App\Events;

use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PresenceChannel;

final class EndGame extends BroadcastEvent
{
    public int $gameId;
    public bool $isSuccess;

    public function __construct(int $gameId, bool $isSuccess)
    {
        $this->gameId = $gameId;
        $this->isSuccess = $isSuccess;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel("games.$this->gameId");
    }

    public function broadcastAs(): string
    {
        return 'game.end';
    }

    public function broadcastWith(): array
    {
        return [
            'status'  => $this->isSuccess,
            'message' => $this->getMessage(),
        ];
    }

    private function getMessage(): string
    {
        return $this->isSuccess
            ? "Игра успешно завершена!"
            : "Ошибка при завершении игры.";
    }
}