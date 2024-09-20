<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\LoginDto;
use App\Parents\Request;

final class LoginRequest extends Request
{
    public function rules(): array
    {
        return [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    public function toDto(): LoginDto
    {
        return LoginDto::fromRequest($this);
    }
}
