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
    public function run(array $user_ids, bool $is_online): void
    {
        User::query()
            ->whereIn('id', $user_ids)
            ->update(['is_online' => $is_online]);
    }
}