<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\ResetPasswordDto;
use App\Exceptions\HttpException;
use App\Models\RefreshToken;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

final class ResetPasswordService extends Service
{
    /**
     * Обновляет пароль пользователя и блокирует все сессии
     */
    public function run(ResetPasswordDto $dto): void
    {
        $status = Password::reset($dto->toArray(), function (User $user, string $password) {
            $user->password = Hash::make($password);
            $user->saveOrFail();

            $this->blockUserSessions($user->id);
        });

        if ($status === Password::INVALID_USER) {
            throw new HttpException(404, 'Пользователь с данной электронной почтой не найден');
        }
        if ($status === Password::INVALID_TOKEN) {
            // Либо токен не найден, либо его срок действия истек
            throw new HttpException(400, 'Неверный или истекший токен сброса пароля');
        }
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