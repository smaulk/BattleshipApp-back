<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Enums\FriendshipStatus;
use App\Exceptions\HttpException;
use App\Models\Friendship;
use App\Services\Abstract\FriendshipService;

final class AcceptFriendshipService extends FriendshipService
{
    public function run(FriendshipDto $dto): void
    {
        $this->validateUsers($dto->userId, $dto->friendId);
        $this->acceptFriendship($dto->userId, $dto->friendId);
    }

    private function acceptFriendship(int $userId, int $friendId): void
    {
        $friendship = $this->getFriendship($userId, $friendId);
        if ($friendship->status === FriendshipStatus::FRIEND) {
            return;
        }
        if (!$this->canAcceptFriendship($friendship, $userId, $friendId)) {
            throw new HttpException(400, "Нельзя принять отправленную заявку");
        }

        $friendship->status = FriendshipStatus::FRIEND;
        $friendship->saveOrFail();
    }

    private function getFriendship(int $userId, int $friendId): Friendship
    {
        return Friendship::findByUsers($userId, $friendId)->firstOrFail();
    }

    private function canAcceptFriendship(Friendship $friendship, int $userId, int $friendId): bool
    {
        $expectedStatus = $userId < $friendId
            ? FriendshipStatus::REQ_UID2
            : FriendshipStatus::REQ_UID1;

        return $friendship->status === $expectedStatus;
    }
}