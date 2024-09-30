<?php

namespace App\Http\Requests;

use App\Dto\CreateFriendshipDto;
use App\Parents\Request;

class CreateFriendshipRequest extends Request
{

    public function rules(): array
    {
        return [
            'friendId' => 'required|int',
        ];
    }

    public function toDto(): CreateFriendshipDto
    {
        return CreateFriendshipDto::fromRequest($this);
    }
}
