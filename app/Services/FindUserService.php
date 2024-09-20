<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Parents\Service;

final class FindUserService extends Service
{
    public function run(int $userId): User
    {
        return User::query()->findOrFail($userId);
    }
}
