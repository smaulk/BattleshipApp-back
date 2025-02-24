<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\GameInvitation;

class DeleteAllInvitesService
{
    public function run(array $userIds): void
    {
        GameInvitation::query()
            ->whereIn('sender_id', $userIds)
            ->delete();
    }
}