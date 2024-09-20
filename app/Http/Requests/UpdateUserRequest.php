<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\UpdateUserDto;

final class UpdateUserRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'min:3', 'max:18'],
            'email'    => ['required', 'email', 'max:255'],
        ];
    }

    public function toDto(): UpdateUserDto
    {
        return UpdateUserDto::fromRequest($this);
    }
}
