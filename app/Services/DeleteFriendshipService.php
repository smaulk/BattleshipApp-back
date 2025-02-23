<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Exceptions\HttpException;
use App\Models\Friendship;
use App\Services\Abstract\FriendshipService;

final class DeleteFriendshipService extends FriendshipService
{
    public function run(FriendshipDto $dto): void
    {
        if (!$this->deleteFriendship($dto)) {
            throw new HttpException(500);
        }
    }

    private function deleteFriendship(FriendshipDto $dto): bool
    {
        $friendship = Friendship::findByUsers($dto->userId, $dto->friendId)->first();
        // Если записи нет, возвращаем true, так как ошибок не возникло
        if (!$friendship) {
            return true;
        }

        return $friendship->delete();
    }
}