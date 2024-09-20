<?php

namespace App\Http\Requests;

use App\Dto\VerifyEmailDto;

class VerifyEmailRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'hash' => 'required|string',
        ];
    }

    public function toDto(): VerifyEmailDto
    {
        return VerifyEmailDto::fromRequest($this);
    }
}
