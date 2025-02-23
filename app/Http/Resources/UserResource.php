<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\FriendshipStatus;
use App\Models\User;
use App\Parents\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;
        // Если пользователь совпадает с авторизованным
        $currentUserId = $request->user()?->getKey();
        $isCurrentUser = $currentUserId === $user->id;
        $isFriendshipRoute = Str::contains($request->path(), ['/friends', '/out-requests', '/in-requests']);

        return [
            'id'        => $user->id,
            'nickname'  => $user->nickname,
            'avatarUrl' => $this->whenAppended('avatar_url'),
            $this->mergeWhen($isCurrentUser, [
                'email'      => $user->email,
                'isVerified' => $user->hasVerifiedEmail(),
            ]),
            'is_online' => $this->when(
                !$isCurrentUser && isset($user->is_online),
                $user->is_online
            ),

            'friendshipType' => $this->when(
                !$isFriendshipRoute && !$isCurrentUser && $currentUserId,
                fn() => $user->status ?
                    FriendshipStatus::fromName($user->status)->toType($currentUserId < $user->id)
                    : null,
            ),

            'statistic' => $this->whenLoaded('statistic', fn() => [
                'games'        => $user->statistic->games,
                'wins'         => $user->statistic->wins,
                'losses'       => $user->statistic->losses,
                'draws'        => $user->statistic->draws,
                'abandonments' => $user->statistic->abandonments,
                'points'       => $user->statistic->points,
            ]),
        ];
    }
}
