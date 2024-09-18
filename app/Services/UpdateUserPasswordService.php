<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\UpdateUserPasswordDto;
use App\Exceptions\HttpException;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

final class UpdateUserPasswordService extends Service
{
    public function run(UpdateUserPasswordDto $dto): void
    {
        try {
            $user = $this->getUserById($dto->userId);
        } catch (ModelNotFoundException) {
            throw new HttpException(404, 'Пользователь не найден');
        }

        if (!Hash::check($dto->currentPassword, $user->password)) {
            throw new HttpException(422, 'Неверный текущий пароль');
        }

        if ($dto->newPassword === $dto->currentPassword) {
            throw new HttpException(422, 'Новый пароль не должен совпадать с текущим');
        }

        try {
            $user->password = Hash::make($dto->newPassword);
            $user->saveOrFail();
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500);
        }

        $this->blockUserSessions($dto->userId);
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

    private function blockUserSessions(int $userId): void
    {
        RefreshToken::query()
            ->where('user_id', $userId)
            ->update([
                'is_blocked' => 1,
            ]);
    }
}