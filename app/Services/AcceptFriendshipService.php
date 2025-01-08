<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Enums\FriendshipStatus;
use Illuminate\Support\Facades\DB;

final class AcceptFriendshipService extends FriendshipService
{
    public function run(FriendshipDto $dto): void
    {
        $this->checkIds($dto->userId, $dto->friendId);
        $this->acceptFriendship($dto->userId, $dto->friendId);
    }

    private function acceptFriendship(int $uid1, int $uid2): void
    {
        [$minId, $maxId] = sort_nums($uid1, $uid2);
        $status = $minId === $uid1
            ? FriendshipStatus::REQ_UID2
            : FriendshipStatus::REQ_UID1;

        // Если такая запись существует, обновляем статус
        DB::table('friendships')
            ->where('uid1', $minId)
            ->where('uid2', $maxId)
            ->where('status', $status)
            ->update(['status' => FriendshipStatus::FRIEND]);
    }
}