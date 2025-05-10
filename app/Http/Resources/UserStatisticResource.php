<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\UserStatistic;
use App\Parents\JsonResource;
use Illuminate\Http\Request;

class UserStatisticResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var UserStatistic $statistic */
        $statistic = $this->resource;

        return [
            'games'  => $statistic->games,
            'wins'   => $statistic->wins,
            'losses' => $statistic->losses,
            'points' => $statistic->points,

            'user' => $this->whenLoaded('user', fn() => [
                'id'        => $statistic->user->id,
                'nickname'  => $statistic->user->nickname,
                'avatarUrl' => $statistic->user->avatar_url,
                'isOnline'  => $statistic->user->is_online,
            ]),
        ];
    }
}