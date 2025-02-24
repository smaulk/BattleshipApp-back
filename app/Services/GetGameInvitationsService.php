<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\GameInvitation;
use App\Parents\Service;
use Illuminate\Support\Collection;

final class GetGameInvitationsService extends Service
{
    public function run(int $userId, ?int $type = null): Collection
    {
        return GameInvitation::query()
            ->when(is_null($type), function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->when($type === 1, function ($query) use ($userId) {
                $query->where('sender_id', $userId);
            })
            ->when($type === 2, function ($query) use ($userId) {
                $query->where('receiver_id', $userId);
            })
            ->orderByDesc('invited_at')
            ->get();
    }
}