<?php

namespace App\Http\Requests;

use App\Dto\VerifyEmailDto;
use App\Parents\Request;

class VerifyEmailRequest extends Request
{
    public function rules(): array
    {
        return [
            'hash'       => 'required|string',
            'expiration' => 'required|int',
            'signature'  => 'required|string',
        ];
    }

    public function toDto(): VerifyEmailDto
    {
        return VerifyEmailDto::fromRequest($this);
    }
}
