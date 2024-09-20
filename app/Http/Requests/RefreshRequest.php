<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\RefreshDto;
use App\Parents\Request;

final class RefreshRequest extends Request
{
    public function rules(): array
    {
        return [
            'refreshToken' => 'required|string',
        ];
    }

    public function toDto(): RefreshDto
    {
        return RefreshDto::fromRequest($this);
    }
}
