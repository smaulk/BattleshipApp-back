<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Dto\SendNotifyDto;
use App\Events\SendNotify;
use App\Models\GameInvitation;
use App\Models\User;
use App\Services\Abstract\UsersService;

final class CreateGameInvitationService extends UsersService
{
    public function run(FriendshipDto $dto): void
    {
        $this->validateUsers($dto->userId, $dto->friendId);

        $result = GameInvitation::updateOrInsert(
            ['sender_id' => $dto->userId, 'receiver_id' => $dto->friendId],
            ['invited_at' => now()]
        );

        if ($result) {
            $this->sendNotify($dto);
        }
    }

    private function sendNotify(FriendshipDto $dto): void
    {
        $sender = User::select(['id', 'nickname'])->find($dto->userId);
        if ($sender) {
            SendNotify::broadcast(SendNotifyDto::fromArray([
                'senderId' => $sender->id,
                'receiverId' => $dto->friendId,
                'message' => "{$sender->nickname} приглашает вас в игру!",
                'event' => 'create.invite',
            ]));
        }
    }
}