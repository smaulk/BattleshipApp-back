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
        $isCurrentUser = $request->user()?->getKey() === $user->getKey();

        return [
            'id'        => $user->id,
            'nickname'  => $user->nickname,
            'avatarUrl' => $this->whenAppended('avatar_url'),
            $this->mergeWhen($isCurrentUser, [
                'email'       => $user->email,
                'isVerified' => $user->hasVerifiedEmail(),
            ]),
        ];
    }
}
