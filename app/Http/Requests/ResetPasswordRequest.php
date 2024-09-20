<?php

namespace App\Http\Requests;

use App\Dto\ResetPasswordDto;
use App\Parents\Request;
use Illuminate\Validation\Rules\Password;

final class ResetPasswordRequest extends Request
{

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ];
    }

    public function toDto(): ResetPasswordDto
    {
        return ResetPasswordDto::fromRequest($this);
    }
}
