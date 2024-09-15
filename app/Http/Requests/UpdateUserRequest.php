<?php

namespace App\Http\Requests;

use App\Dto\UpdateUserDto;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateUserRequest extends Request
{
    public function authorize(): bool
    {
        // Проверяем, что id авторизованного пользователя совпадает с переданным id пользователя
        return $this->user()?->getAuthIdentifier() === (int)$this->route('userId');
    }

    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'min:3', 'max:28'],
            'email'    => ['required', 'email', 'max:255'],
        ];
    }

    public function toDto(): UpdateUserDto
    {
        return UpdateUserDto::fromRequest($this);
    }
}
