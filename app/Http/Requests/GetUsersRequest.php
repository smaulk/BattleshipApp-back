<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\GetUsersDto;
use App\Parents\Request;

final class GetUsersRequest extends Request
{
    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'min:1', 'max:18']
        ];
    }

    public function toDto(): GetUsersDto
    {
        return GetUsersDto::fromRequest($this);
    }
}
