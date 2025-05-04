<?php
declare(strict_types=1);

namespace App\Events;

use App\Dto\ShotDataDto;
use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PresenceChannel;

final class SendMoveResult extends BroadcastEvent
{
    public int $userId;
    public int $gameId;
    public ShotDataDto $shotData;

    public function __construct(int $userId, int $gameId, ShotDataDto $shotData)
    {
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->shotData = $shotData;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel("games.$this->gameId");
    }

    public function broadcastAs(): string
    {
        return 'game.move.result';
    }

    public function broadcastWith(): array
    {
        return [
            'userId'   => $this->userId,
            'shotData' => [
                'status'    => $this->shotData->status,
                'ship'      => [
                    'id'       => $this->shotData->ship?->id,
                    'size'     => $this->shotData->ship?->size,
                    'position' => $this->shotData->ship?->position,
                ],
                'startCell' => [
                    'col' => $this->shotData->startCell?->col,
                    'row' => $this->shotData->startCell?->row,
                ],
            ],
        ];
    }
}