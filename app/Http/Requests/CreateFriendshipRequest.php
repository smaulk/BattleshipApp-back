<?php

namespace App\Http\Requests;

use App\Dto\FriendshipDto;
use App\Parents\Request;

class CreateFriendshipRequest extends Request
{
    public function rules(): array
    {
        return [
            'friendId' => 'required|int',
        ];
    }

    public function toDto(): FriendshipDto
    {
        return FriendshipDto::fromCreateRequest($this);
    }
}
