<?php

namespace App\Http\Services;

use App\Dto\LoginDto;
use App\Exceptions\HttpException;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function login(LoginDto $dto): string
    {
        $accessToken = Auth::attempt([
            'nickname' => $dto->nickname,
            'password' => $dto->password
        ]);

        if ($accessToken === false) {
            throw new HttpException(401, 'Некорректные данные для входа');
        }

        return $accessToken;
    }
}
