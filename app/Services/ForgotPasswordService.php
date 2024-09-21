<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Support\Facades\Password;

final class ForgotPasswordService extends Service
{
    /**
     * Отправляет письмо для сброса пароля, на указанную почту
     */
    public function run(string $email): void
    {
        // Создает новый токен для сброса пароля и отправляет письмо с ссылкой на почту
        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::INVALID_USER) {
            throw new HttpException(404, 'Пользователь с данной электронной почтой не найден');
        }
        if($status === Password::RESET_THROTTLED){
            throw new HttpException(429, 'Слишком много запросов на сброс пароля');
        }
    }
}