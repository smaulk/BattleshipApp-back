<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\VerifyEmailDto;
use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;

final class VerifyEmailService extends Service
{
    public function run(VerifyEmailDto $dto): void
    {
        $user = User::query()->findOrFail($dto->userId);

        // Проверка, что данные корректные
        if (
            !$this->isIdValid($user, $dto->id) ||
            !$this->isHashValid($user, $dto->hash)
        ) {
            throw new HttpException(403, 'Неверные данные для верификации');
        }

        // Подтверждаем почту
        if (!$user->hasVerifiedEmail()) {
            if (!$user->markEmailAsVerified()) {
                throw new HttpException(500);
            }
        }
    }


    protected function isIdValid(User $user, string $id): bool
    {
        return hash_equals((string)$user->getKey(), $id);
    }

    protected function isHashValid(User $user, string $hash): bool
    {
        return hash_equals(sha1($user->getEmailForVerification()), $hash);
    }
}