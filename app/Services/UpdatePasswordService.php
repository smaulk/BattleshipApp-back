<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\UpdateUserPasswordDto;
use App\Exceptions\HttpException;
use App\Models\RefreshToken;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Support\Facades\Hash;
use Throwable;


final class UpdatePasswordService extends Service
{
    /**
     * @throws Throwable
     */
    public function run(UpdateUserPasswordDto $dto): void
    {
        $user = User::query()->findOrFail($dto->userId);

        if (!Hash::check($dto->currentPassword, $user->password)) {
            throw new HttpException(422, 'Неверный текущий пароль');
        }
        if ($dto->newPassword === $dto->currentPassword) {
            throw new HttpException(422, 'Новый пароль не должен совпадать с текущим');
        }

        $user->password = Hash::make($dto->newPassword);
        $user->saveOrFail();
        $this->blockUserSessions($dto->userId);
    }

    /**
     * Блокирует все сессии пользователя
     */
    private function blockUserSessions(int $userId): void
    {
        RefreshToken::query()
            ->where('user_id', $userId)
            ->update([
                'is_blocked' => 1,
            ]);
    }
}