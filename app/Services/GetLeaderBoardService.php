<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\UserStatistic;
use App\Parents\Service;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

final class GetLeaderBoardService extends Service
{
    public function run(): Collection
    {
        return UserStatistic::query()
            ->select(['user_id', 'games', 'wins', 'losses', 'points'])
            ->with([
                'user' => function (BelongsTo $query) {
                    $query->select(['id', 'nickname', 'avatar_filename']);
                }
            ])
            ->orderByDesc('points')
            ->limit(UserStatistic::LEADERBOARD_COUNT)
            ->get();
    }
}