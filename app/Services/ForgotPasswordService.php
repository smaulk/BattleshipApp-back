<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Support\Facades\Password;

final class ForgotPasswordService extends Service
{
    public function run(string $email): void
    {
        if (!$this->isExistsUser($email)) {
            throw new HttpException(404, 'Пользователь с такой электронной почтой не найден');
        }

        $status = Password::sendResetLink(['email' => $email]);
        if ($status !== Password::RESET_LINK_SENT) {
            throw new HttpException(500);
        }
    }

    private function isExistsUser(string $email): bool
    {
        return User::query()
            ->where('email', $email)
            ->exists();
    }
}