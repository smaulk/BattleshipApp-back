<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use App\Parents\JsonResource;
use Illuminate\Http\Request;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;
        // Если пользователь совпадает с авторизованным
        $currentUserId = $request->user()?->getKey();
        $isCurrentUser = $currentUserId === $user->id;

        return [
            'id'        => $user->id,
            'nickname'  => $user->nickname,
            'avatarUrl' => $this->whenAppended('avatar_url'),
            $this->mergeWhen($isCurrentUser, [
                'email'      => $user->email,
                'isVerified' => $user->hasVerifiedEmail(),
            ]),

            'friendshipType' => $this->when(
                !$isCurrentUser && !is_null($currentUserId),
                function () use($user, $currentUserId) {
                    $type = $user->friendshipType((int)$currentUserId);
                    return $type?->name;
                },
            ),
        ];
    }
}
