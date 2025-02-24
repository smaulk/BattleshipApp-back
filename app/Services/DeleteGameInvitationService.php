<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Exceptions\HttpException;
use App\Models\GameInvitation;
use App\Parents\Service;
use Illuminate\Database\Eloquent\Builder;

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
        if ($this->getInvitationsQuery($dto)->doesntExist()) {
            return true;
        }

        return (bool)$this->getInvitationsQuery($dto)->delete();
    }

    private function getInvitationsQuery(FriendshipDto $dto): Builder
    {
        return GameInvitation::query()
            ->findByUsers($dto->userId, $dto->friendId)
            ->orWhere(function ($query) use ($dto) {
                $query->findByUsers($dto->friendId, $dto->userId);
            });
    }
}