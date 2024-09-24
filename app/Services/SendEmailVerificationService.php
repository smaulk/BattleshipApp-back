<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;

final class SendEmailVerificationService extends Service
{
    /**
     * Отправляет письмо на почту пользователя с ссылкой на подтверждение почты
     */
    public function run(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        if ($user->hasVerifiedEmail()) {
            throw new HttpException(422, 'Электронная почта уже подтверждена');
        }

        $user->sendEmailVerificationNotification();
    }
}