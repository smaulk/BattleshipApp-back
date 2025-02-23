<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\CreateFriendshipRequest;
use App\Parents\Dto;
use App\Parents\Request;

final readonly class FriendshipDto extends Dto
{
    public int $userId;
    public int $friendId;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->userId = (int)$data['userId'];
        $dto->friendId = (int)$data['friendId'];

        return $dto;
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray([
            'userId'   => $request->user()?->getAuthIdentifier(),
            'friendId' => $request->route('friendId')
        ]);
    }

    public static function fromCreateRequest(CreateFriendshipRequest $request): self
    {
        return self::fromArray([
            'userId'   => $request->user()?->getAuthIdentifier(),
            'friendId' => $request->validated('friendId')
        ]);
    }
}