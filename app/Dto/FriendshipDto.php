<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\CreateFriendshipRequest;
use App\Parents\Dto;
use App\Parents\Request;

final readonly class FriendshipDto extends Dto
{
    public function __construct(
        public int $userId,
        public int $friendId
    ){}

    public static function fromRequest(Request $request): FriendshipDto
    {
        return new self(
            (int)$request->user()?->getAuthIdentifier(),
            (int)$request->route('friendId')
        );
    }

    public static function fromCreateRequest(CreateFriendshipRequest $request): FriendshipDto
    {
        return new self(
            (int)$request->user()?->getAuthIdentifier(),
            (int)$request->validated('friendId')
        );
    }
}