<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class FindUserService extends Service
{
    public function run(int $userId): User
    {
        try {
            $user = $this->getUserById($userId);
        } catch (ModelNotFoundException) {
            throw new HttpException(404, 'Пользователь не найден');
        }

        return $user;
    }

    /**
     * @throws ModelNotFoundException
     */
    private function getUserById(int $userId): User
    {
        /** @var User */
        return User::query()->findOrFail($userId);
    }
}
