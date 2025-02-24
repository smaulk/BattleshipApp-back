<?php
declare(strict_types=1);

namespace App\Classes\WebSocket\Channels;

use App\Models\User;
use App\Parents\Channel;
use App\Services\SaveOnlineService;

class OnlineChannel extends Channel
{
    public function join(User $user, int $userId): bool
    {
        $is_available = $user->id === $userId;
        if ($is_available) {
            (new SaveOnlineService())->run([$userId], true);
        }
        return $is_available;
    }
}