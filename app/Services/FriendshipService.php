<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;

abstract class FriendshipService extends Service
{
    protected function checkIds(int $userId, int $friendId): void
    {
        if ($userId === $friendId) {
            throw new HttpException(400, 'Идентификаторы пользователей совпадают');
        }

        if (
            !User::query()->where('id', $userId)->exists() ||
            !User::query()->where('id', $friendId)->exists()
        ) {
            throw new HttpException(400, User::getNotFoundMessage());
        }

    }
}