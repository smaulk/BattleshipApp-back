<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Parents\Service;

final class FindUserService extends Service
{
    public function run(int $userId, ?int $currentUserId): User
    {
        return User::query()
            ->select('users.*')
            ->with([
                'statistic' => function ($query) {
                    $query ->select(['user_id', 'games', 'wins', 'losses', 'draws', 'points']);
                }
            ])
            ->when($currentUserId && $currentUserId !== $userId, function ($query) use ($currentUserId) {
                $query->addSelect('friendships.status')->joinFriendships($currentUserId);
            })
            ->findOrFail($userId);
    }
}
