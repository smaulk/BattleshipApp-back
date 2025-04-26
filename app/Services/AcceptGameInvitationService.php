<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Events\CreateRoom;
use App\Models\GameInvitation;
use App\Parents\Service;
use Illuminate\Support\Facades\DB;

final class AcceptGameInvitationService extends Service
{
    public function run(FriendshipDto $dto): void
    {
        // Проверка, что приглашение существует
        GameInvitation::findByUsers($dto->friendId, $dto->userId)->firstOrFail();

        DB::transaction(function () use ($dto) {
            $roomId = (new CreateRoomService())->run($dto->userId, $dto->friendId);
            CreateRoom::broadcast($dto->userId, $dto->friendId, $roomId);
        });
    }
}