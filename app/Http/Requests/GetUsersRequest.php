<?php

namespace App\Http\Requests;

use App\Dto\GetUsersDto;

final class GetUsersRequest extends Request
{
    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'min:1', 'max:28']
        ];
    }

    public function toDto(): GetUsersDto
    {
        return GetUsersDto::fromRequest($this);
    }
}
