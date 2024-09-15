<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateRefreshTokenDto;
use App\Dto\LoginDto;
use App\Exceptions\HttpException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

final class LoginService extends Service
{
    /**
     *  Авторизация пользователя по учетным данным, возвращает токен доступа и обновления
     */
    public function run(LoginDto $dto): array
    {
        $accessToken = $this->login($dto);
        try {
            $refreshToken = (new CreateRefreshTokenService())
                ->run(new CreateRefreshTokenDto(
                    null, Auth::id(), $dto->ipAddress, $dto->userAgent
                ));
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500);
        }

        return [$accessToken, $refreshToken->ulid];
    }

    /**
     * Авторизация по данным из DTO
     */
    private function login(LoginDto $dto): string
    {
        /** @var string|false $accessToken */
        $accessToken = Auth::attempt([
            'email'    => $dto->email,
            'password' => $dto->password
        ]);

        if ($accessToken === false) {
            throw new HttpException(401, 'Некорректные данные для входа');
        }

        return $accessToken;
    }
}
