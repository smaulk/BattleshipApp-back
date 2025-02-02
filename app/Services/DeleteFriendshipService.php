<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Exceptions\HttpException;
use App\Models\Friendship;

final class DeleteFriendshipService extends FriendshipService
{
    public function run(FriendshipDto $dto): void
    {
        $this->checkIds($dto->userId, $dto->friendId);
        if (!$this->deleteFriendship($dto->userId, $dto->friendId)) {
            throw new HttpException(500);
        }
    }

    private function deleteFriendship(int $userId, int $friendId): bool
    {
        $friendship = Friendship::findByUsers($userId, $friendId)->first();
        // Если записи нет, возвращаем true, так как ошибок не возникло
        if (!$friendship) {
            return true;
        }

        return $friendship->delete();
    }
}