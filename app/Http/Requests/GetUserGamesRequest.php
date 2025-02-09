<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\GetUserGamesDto;

final class GetUserGamesRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'startId' => 'nullable|integer',
            'type'  => 'nullable|integer'
        ];
    }

    public function toDto(): GetUserGamesDto
    {
        return GetUserGamesDto::fromRequest($this);
    }
}