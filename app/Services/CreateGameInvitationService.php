<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\FriendshipDto;
use App\Dto\SendNotifyDto;
use App\Enums\FriendshipStatus;
use App\Events\SendNotify;
use App\Exceptions\HttpException;
use App\Models\Friendship;
use App\Models\GameInvitation;
use App\Models\User;
use App\Parents\Service;

final class CreateGameInvitationService extends Service
{
    public function run(FriendshipDto $dto): void
    {
        $this->validate($dto);

        if ($this->checkInviteFromFriendExists($dto)) {
            (new AcceptGameInvitationService())->run($dto, false);
            return;
        }

        $result = GameInvitation::updateOrInsert(
            ['sender_id' => $dto->userId, 'receiver_id' => $dto->friendId],
            ['invited_at' => now()]
        );

        if ($result) {
            $this->sendNotify($dto);
        }
    }

    private function validate(FriendshipDto $dto): void
    {
        if (!Friendship::query()
            ->findByUsers($dto->userId, $dto->friendId)
            ->where('status', FriendshipStatus::FRIEND)
            ->exists()
        ) {
            throw new HttpException(403, "Пригласить в игру можно только друга");
        }
    }

    private function checkInviteFromFriendExists(FriendshipDto $dto): bool
    {
        return GameInvitation::findByUsers($dto->friendId, $dto->userId)->exists();
    }

    private function sendNotify(FriendshipDto $dto): void
    {
        $sender = User::select(['id', 'nickname'])->find($dto->userId);
        if ($sender) {
            SendNotify::broadcast(SendNotifyDto::fromArray([
                'senderId'   => $sender->id,
                'receiverId' => $dto->friendId,
                'message'    => "{$sender->nickname} приглашает вас в игру!",
                'event'      => 'create.invite',
            ]));
        }
    }
}