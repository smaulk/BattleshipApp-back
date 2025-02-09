<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Enums\FriendshipStatus;
use App\Enums\FriendshipType;
use App\Models\Friendship;
use App\Parents\Service;
use Illuminate\Support\Facades\DB;

final class GetFriendshipTypeService extends Service
{
    public function run(FriendshipDto $dto): FriendshipType|null
    {
        /** @var Friendship $friendship */
        $friendship = Friendship::query()
            ->select(['id', 'uid1', 'uid2', 'status'])
            ->findByUsers($dto->userId, $dto->friendId)
            ->first();

        return $friendship?->status->toType($friendship->uid1 === $dto->friendId);
    }
}