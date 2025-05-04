<?php
declare(strict_types=1);

namespace App\Events;

use App\Dto\ColRowDataDto;
use App\Dto\ShotDataDto;
use App\Parents\BroadcastEvent;
use Illuminate\Broadcasting\PresenceChannel;

final class SendMoveData extends BroadcastEvent
{
    public int $userId;
    public int $gameId;
    public ColRowDataDto $cell;

    public function __construct(int $userId, int $gameId, ColRowDataDto $cell)
    {
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->cell = $cell;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel("games.$this->gameId");
    }

    public function broadcastAs(): string
    {
        return 'game.move.data';
    }

    public function broadcastWith(): array
    {
        return [
            'userId'   => $this->userId,
            'cellData' => [
                'col' => $this->cell->col,
                'row' => $this->cell->row,
            ],
        ];
    }
}