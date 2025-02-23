<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\EndGameDto;
use App\Parents\Request;

final class FinishGameRequest extends Request
{
    public function rules(): array
    {
        return [
            'type'   => 'required|int',
        ];
    }

    public function toDto(): EndGameDto
    {
        return EndGameDto::fromRequest($this);
    }
}