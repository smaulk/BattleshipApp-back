<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\GetUserGamesDto;
use App\Enums\GameType;
use Illuminate\Validation\Rules\Enum;

final class GetUserGamesRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'startId' => ['nullable', 'integer'],
            'type'    => ['nullable', new Enum(GameType::class)]
        ];
    }

    public function toDto(): GetUserGamesDto
    {
        return GetUserGamesDto::fromRequest($this);
    }
}