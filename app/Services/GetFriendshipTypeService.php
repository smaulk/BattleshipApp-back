<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Enums\FriendshipStatus;
use App\Enums\FriendshipType;
use App\Parents\Service;
use Illuminate\Support\Facades\DB;

final class GetFriendshipTypeService extends Service
{
    public function run(FriendshipDto $dto): FriendshipType|null
    {
        [$minId, $maxId] = sort_nums($dto->userId, $dto->friendId);
        $friendship = DB::table('friendships')
            ->select('status')
            ->where('uid1', $minId)
            ->where('uid2', $maxId)
            ->first();

        if (is_null($friendship)) {
            return null;
        }
        $isRequester = $minId === $dto->friendId; // Флаг, указывающий, кто является инициатором

        return match ($friendship->status) {
            FriendshipStatus::REQ_UID1->name => $isRequester ? FriendshipType::OUTGOING : FriendshipType::INCOMING,
            FriendshipStatus::REQ_UID2->name => $isRequester ? FriendshipType::INCOMING : FriendshipType::OUTGOING,
            FriendshipStatus::FRIEND->name   => FriendshipType::FRIEND,
            default                          => null,
        };
    }
}