<?php

namespace App\Http\Requests;

use App\Dto\GetUsersByFriendshipDto;
use App\Enums\FriendshipType;
use Illuminate\Foundation\Http\FormRequest;

class GetUsersByFriendshipRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'startId'  => 'nullable|integer',
            'nickname' => 'nullable|string',
        ];
    }

    public function toDto(FriendshipType $type): GetUsersByFriendshipDto
    {
        return GetUsersByFriendshipDto::fromRequest($this, $type);
    }
}
