<?php
declare(strict_types=1);

namespace App\Services;

use App\Classes\AvatarManager;
use App\Exceptions\HttpException;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;

final class DeleteUserAvatarService extends Service
{

    public function run(int $userId): void
    {
        try {
            $user = $this->getUserById($userId);
            $filename = $user->avatar_filename;
            $user->avatar_filename = null;
            $user->saveOrFail();
        } catch (ModelNotFoundException) {
            throw new HttpException(404, 'Пользователь не найден');
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500);
        }

        (new AvatarManager())->delete($filename);
    }

    /**
     * Получить пользователя по id
     * @throws ModelNotFoundException
     */
    private function getUserById(int $userId): User
    {
        /** @var User */
        return User::query()->findOrFail($userId);
    }
}
