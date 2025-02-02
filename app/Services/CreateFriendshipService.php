<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Enums\FriendshipStatus;
use App\Exceptions\HttpException;
use App\Models\Friendship;

final class CreateFriendshipService extends FriendshipService
{
    public function run(FriendshipDto $dto): void
    {
        $this->checkIds($dto->userId, $dto->friendId);
        if ($this->isExistsFriendship($dto->userId, $dto->friendId)) {
            throw new HttpException(400, 'Запись уже существует');
        }

        $this->createFriendRequest($dto->userId, $dto->friendId);
    }

    private function isExistsFriendship(int $uid1, int $uid2): bool
    {
        return Friendship::findByUsers($uid1, $uid2)->exists();
    }

    private function createFriendRequest(int $userId, int $friendId): void
    {
        [$minId, $maxId] = sort_nums($userId, $friendId);
        $status = $minId === $userId
            ? FriendshipStatus::REQ_UID1
            : FriendshipStatus::REQ_UID2;

        $friendship = new Friendship();
        $friendship->uid1 = $minId;
        $friendship->uid2 = $maxId;
        $friendship->status = $status;
        $friendship->saveOrFail();
    }
}