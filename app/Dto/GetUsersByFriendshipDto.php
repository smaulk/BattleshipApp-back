<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\FriendshipType;
use App\Http\Requests\GetUsersByFriendshipRequest;
use App\Parents\Dto;
use App\Parents\Request;

final readonly class GetUsersByFriendshipDto extends Dto
{
    public int $userId;
    public FriendshipType $type;
    public ?int $startId;
    public ?string $nickname;
    public ?bool $is_online;

    public static function fromRequest(GetUsersByFriendshipRequest $request, FriendshipType $type): GetUsersByFriendshipDto
    {
        $startId = $request->validated('startId');
        $is_online = $request->validated('is_online');

        $dto = new self();
        $dto->userId = $request->getUserId();
        $dto->type = $type;
        $dto->startId = $startId ? (int)$startId : null;
        $dto->nickname = $request->validated('nickname');
        $dto->is_online = !is_null($is_online) ? (bool)$is_online : null;

        return $dto;
    }
}