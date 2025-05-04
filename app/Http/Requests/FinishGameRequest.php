<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\EndGameDto;
use App\Enums\GameType;
use App\Parents\Request;
use Illuminate\Validation\Rules\Enum;

final class FinishGameRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(GameType::class)],
        ];
    }

    public function toDto(): EndGameDto
    {
        return EndGameDto::fromRequest($this);
    }
}