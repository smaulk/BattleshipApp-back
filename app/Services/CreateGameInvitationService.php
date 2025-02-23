<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Events\InviteGame;
use App\Models\GameInvitation;
use App\Models\User;
use App\Services\Abstract\FriendshipService;

final class CreateGameInvitationService extends FriendshipService
{
    public function run(FriendshipDto $dto): void
    {
        $this->validateUsers($dto->userId, $dto->friendId);

        $result = GameInvitation::updateOrInsert(
            ['sender_id' => $dto->userId, 'receiver_id' => $dto->friendId],
            ['invited_at' => now()]
        );

        if (!$result) {
            return;
        }

        $sender = User::select(['id', 'nickname'])->find($dto->userId);
        if ($sender) {
            InviteGame::broadcast($sender, $dto->friendId);
        }
    }
}