<?php

namespace App\Http\Requests;

use App\Dto\GetUsersByFriendshipDto;
use App\Enums\FriendshipType;

final class GetUsersByFriendshipRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'startId'   => 'nullable|integer',
            'nickname'  => 'nullable|string',
            'is_online' => 'nullable|boolean',
        ];
    }

    public function toDto(FriendshipType $type): GetUsersByFriendshipDto
    {
        return GetUsersByFriendshipDto::fromRequest($this, $type);
    }
}
