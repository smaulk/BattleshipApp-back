<?php
declare(strict_types=1);

namespace App\Classes\WebSocket\Handlers;

use App\Classes\WebSocket\Requests\WebHookRequest;
use App\Parents\WebHookHandler;
use App\Services\SetLoseUserGameService;

class GameWebHookHandler extends WebHookHandler
{
    private const DISCONNECT_EVENT = 'member_removed';

    /**
     * Если пользователь покинул игру, делаем его проигравшим
     */
    public function __invoke(WebHookRequest $request): void
    {
        $pattern = '/^presence-games\.(\d+)/';
        foreach ($request->input('events') as $event) {
            if ($event['name'] != self::DISCONNECT_EVENT) {
                continue;
            }
            if (!preg_match($pattern, $event['channel'], $matches)) {
                continue;
            }
            $gameId = (int)$matches[1];
            $userId = !empty($event['user_id']) ? (int)$event['user_id'] : null;

            if ($gameId && $userId) {
                (new SetLoseUserGameService())->run(
                    $gameId, $userId
                );
            }
        }
    }
}