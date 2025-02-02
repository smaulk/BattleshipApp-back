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
        $friendship = Friendship::query()
            ->select('status')
            ->findByUsers($dto->userId, $dto->friendId)
            ->first();

        if (!$friendship) {
            return null;
        }
        // Флаг, указывающий, кто является инициатором
        $isRequester = $dto->userId > $dto->friendId;

        return match ($friendship->status) {
            FriendshipStatus::REQ_UID1->name => $isRequester ? FriendshipType::OUTGOING : FriendshipType::INCOMING,
            FriendshipStatus::REQ_UID2->name => $isRequester ? FriendshipType::INCOMING : FriendshipType::OUTGOING,
            FriendshipStatus::FRIEND->name   => FriendshipType::FRIEND,
            default                          => null,
        };
    }
}