<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\CreateGameDto;
use App\Parents\Request;

final class CreateGameRequest extends Request
{
    public function rules(): array
    {
        return [
            'uid1'   => 'required|int',
            'uid2'   => 'required|int',
            'status' => 'nullable|int',
        ];
    }

    public function toDto(): CreateGameDto
    {
        return CreateGameDto::fromRequest($this);
    }
}