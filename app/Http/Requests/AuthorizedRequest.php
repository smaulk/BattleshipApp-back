<?php

namespace App\Http\Requests;

use App\Parents\Request;

class AuthorizedRequest extends Request
{
    public function authorize(): bool
    {
        // Проверяем, что id авторизованного пользователя совпадает с переданным id пользователя
        return $this->user()?->getAuthIdentifier() === (int)$this->route('userId');
    }
}
