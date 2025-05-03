<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Exceptions\HttpException;
use App\Models\Friendship;
use App\Services\Abstract\UsersService;
use Illuminate\Support\Facades\DB;

final class DeleteFriendshipService extends UsersService
{
    public function run(FriendshipDto $dto): void
    {
        DB::transaction(function () use ($dto) {
            if (!$this->deleteFriendship($dto)) {
                throw new HttpException(500);
            }

            (new DeleteGameInvitationService())->run($dto);
        });
    }

    private function deleteFriendship(FriendshipDto $dto): bool
    {
        /** @var Friendship | null $friendship */
        $friendship = Friendship::findByUsers($dto->userId, $dto->friendId)->first();
        // Если записи нет, возвращаем true, так как ошибок не возникло
        if (!$friendship) {
            return true;
        }

        return (bool)$friendship->delete();
    }
}