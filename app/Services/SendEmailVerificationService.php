<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;

final class SendEmailVerificationService extends Service
{
    public function run(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        if ($user->hasVerifiedEmail()) {
            throw new HttpException(422, 'Электронная почта уже была подтверждена');
        }

        $user->sendEmailVerificationNotification();
    }
}