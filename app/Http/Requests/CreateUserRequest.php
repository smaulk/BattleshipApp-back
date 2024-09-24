<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\CreateUserDto;
use App\Parents\Request;
use App\Rules\Confirmed;
use Illuminate\Validation\Rules\Password;

final class CreateUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'min:3', 'max:18'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', new Confirmed,
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ];
    }

    public function toDto(): CreateUserDto
    {
        return CreateUserDto::fromRequest($this);
    }
}
