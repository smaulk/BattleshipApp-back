<?php
declare(strict_types=1);

namespace App\Classes\WebSocket\Handlers;

use App\Classes\WebSocket\Requests\WebHookRequest;
use App\Services\RoomQueueService;
use App\Services\SaveOnlineService;

class OnlineHandler extends Handler
{
    private const DISCONNECT_EVENT = 'channel_vacated';

    /**
     * Если пользователь покинул сайт, убираем онлайн
     */
    public function __invoke(WebHookRequest $request): void
    {
        $pattern = '/^private-users\.(\d+)\.online/';
        $userIds = [];
        foreach ($request->input('events') as $event) {
            if ($event['name'] != self::DISCONNECT_EVENT) {
                continue;
            }
            if (!preg_match($pattern, $event['channel'], $matches)) {
                continue;
            }
            $userIds[] = (int)$matches[1];

            (new RoomQueueService())->dequeue((int)$matches[1]);
        }

        if (count($userIds)) {
            (new SaveOnlineService())->run($userIds, false);
        }
    }

}