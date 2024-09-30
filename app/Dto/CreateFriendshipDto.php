<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\CreateFriendshipRequest;
use App\Parents\Dto;

final readonly class CreateFriendshipDto extends Dto
{
    public int $userId;
    public int $friendId;

    public static function fromRequest(CreateFriendshipRequest $request): CreateFriendshipDto
    {
        $dto = new self();
        $dto->userId = (int)$request->user()?->getAuthIdentifier();
        $dto->friendId = (int)$request->validated('friendId');
        return $dto;
    }
}