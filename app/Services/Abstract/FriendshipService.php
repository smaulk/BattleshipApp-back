<?php
declare(strict_types=1);

namespace App\Services\Abstract;

use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class FriendshipService extends Service
{
    protected function validateUsers(int $uid1, int $uid2): void
    {
        if ($uid1 === $uid2) {
            throw new HttpException(400, 'Идентификаторы пользователей совпадают');
        }

        if (
            User::query()
                ->whereIn('id', [$uid1, $uid2])
                ->count() !== 2
        ) {
            throw (new ModelNotFoundException())->setModel(User::class);
        }
    }
}