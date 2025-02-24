<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\GameInvitation;
use App\Parents\Service;

final class DeleteAlIGameInvitationsService extends Service
{
    public function run(array $userIds): void
    {
        GameInvitation::query()
            ->whereIn('sender_id', $userIds)
            ->delete();
    }
}