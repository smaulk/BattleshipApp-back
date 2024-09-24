<?php
declare(strict_types=1);

namespace App\Services;

use App\Classes\VerificationManager;
use App\Dto\VerifyEmailDto;
use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;

final class VerifyEmailService extends Service
{
    /**
     * Подтверждает почту пользователя
     */
    public function run(VerifyEmailDto $dto): void
    {
        if ($dto->exp < time()) {
            throw new HttpException(403, 'Срок для верификации истек');
        }

        $user = User::query()->findOrFail($dto->userId);
        $this->validate($user, $dto);

        if ($user->hasVerifiedEmail()) {
            return;
        }
        // Подтверждаем почту
        if (!$user->markEmailAsVerified()) {
            throw new HttpException(500);
        }
    }

    private function validate(User $user, VerifyEmailDto $dto): void
    {
        $manager = new VerificationManager();
        $hash = $manager->hashString($user->getEmailForVerification());
        $data = $manager->createData($user->getKey(), $hash, $dto->exp);

        // Проверка данных
        if (
            !hash_equals($hash, $dto->hash) ||
            !$manager->checkSign($data, $dto->signature)
        ) {
            throw new HttpException(400, 'Недействительные данные для верификации');
        }
    }
}