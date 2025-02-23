<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Exceptions\HttpException;
use App\Models\GameInvitation;
use App\Parents\Service;

final class DeleteGameInvitationService extends Service
{
    public function run(FriendshipDto $dto): void
    {
        if (!$this->deleteInvite($dto)) {
            throw new HttpException(500);
        }
    }

    private function deleteInvite(FriendshipDto $dto): bool
    {
        // Если записи нет, возвращаем true, так как ошибок не возникло
        if (GameInvitation::findByUsers($dto->friendId, $dto->userId)->doesntExist()) {
            return true;
        }

        return (bool)GameInvitation::findByUsers($dto->friendId, $dto->userId)->delete();
    }
}