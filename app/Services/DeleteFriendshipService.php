<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use Illuminate\Support\Facades\DB;

final class DeleteFriendshipService extends FriendshipService
{
    public function run(FriendshipDto $dto): void
    {
        $this->checkIds($dto->userId, $dto->friendId);
        $this->deleteFriendship($dto->userId, $dto->friendId);
    }

    private function deleteFriendship(int $uid1, int $uid2): void
    {
        [$minId, $maxId] = sort_nums($uid1, $uid2);

        DB::table('friendships')
            ->where('uid1', $minId)
            ->where('uid2', $maxId)
            ->delete();
    }
}