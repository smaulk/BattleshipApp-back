<?php
declare(strict_types=1);

namespace App\Services;

use App\Events\CreateRoom;
use App\Parents\Service;
use Illuminate\Support\Facades\Redis;

final class RoomQueueService extends Service
{
    private const QUEUE = "queue:rooms";

    /**
     * Добавляем игрока в очередь для поиска комнаты, и уведомляем, если комната создана
     */
    public function enqueue(int $userId): void
    {
        $playerId = Redis::lpop(self::QUEUE);
        if (!$playerId || (int)$playerId === $userId) {
            Redis::rpush(self::QUEUE, $userId);
            return;
        }

        $roomId = (new CreateRoomService())->run($userId, (int)$playerId);
        CreateRoom::broadcast($userId, (int)$playerId, $roomId);
    }

    /**
     * Убираем игрока из очереди для поиска противника
     */
    public function dequeue(int $userId): void
    {
        Redis::lrem(self::QUEUE, 0, $userId);
    }
}