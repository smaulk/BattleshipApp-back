<?php
declare(strict_types=1);

namespace App\Http\Requests;


use App\Dto\GetGameInvitationsDto;

final class GetGameInvitationsRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'startId' => 'nullable|integer',
        ];
    }

    public function toDto(?int $type = null): GetGameInvitationsDto
    {
        return GetGameInvitationsDto::fromRequest($this, $type);
    }
}