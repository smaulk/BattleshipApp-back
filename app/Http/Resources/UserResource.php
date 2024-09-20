<?php

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

        return [
            'id'       => $user->id,
            'nickname' => $user->nickname,
            'avatar_url'   => $this->whenAppended('avatar_url'),
        ];
    }
}
