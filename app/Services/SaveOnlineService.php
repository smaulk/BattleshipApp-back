<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Parents\Service;

final class SaveOnlineService extends Service
{
    /**
     * Устанавливает онлайн пользователя
     */
    public function run(array $userIds, bool $isOnline): void
    {
        User::query()
            ->whereIn('id', $userIds)
            ->update(['is_online' => $isOnline]);
    }
}