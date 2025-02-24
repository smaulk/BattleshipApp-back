<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Dto\SendNotifyDto;
use App\Enums\FriendshipStatus;
use App\Events\SendNotify;
use App\Exceptions\HttpException;
use App\Models\Friendship;
use App\Models\User;
use App\Services\Abstract\FriendshipService;

final class CreateFriendshipService extends FriendshipService
{
    public function run(FriendshipDto $dto): void
    {
        $this->validateUsers($dto->userId, $dto->friendId);
        if ($this->isExistsFriendship($dto->userId, $dto->friendId)) {
            throw new HttpException(400, 'Запись уже существует');
        }

        $this->createFriendRequest($dto->userId, $dto->friendId);
        $this->sendNotify($dto);
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

    private function sendNotify(FriendshipDto $dto): void
    {
        $sender = User::select(['id', 'nickname'])->find($dto->userId);
        if ($sender) {
            SendNotify::broadcast(SendNotifyDto::fromArray([
                'senderId'   => $sender->id,
                'receiverId' => $dto->friendId,
                'message'    => "{$sender->nickname} хочет добавить вас в друзья!",
                'event'      => 'create.request',
            ]));
        }
    }
}