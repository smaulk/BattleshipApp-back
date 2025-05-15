<?php

namespace App\Http\Resources;

use App\Classes\AvatarManager;
use App\Models\Game;
use App\Parents\JsonResource;
use Illuminate\Http\Request;

class UserGameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Game $game */
        $game = $this->resource;

        return [
            'id'             => $game->id,
            'type'           => $game->status->toType($game->uid1 !== $game->rivalId),
            'rivalId'        => $game->rivalId,
            'rivalNickname'  => $game->nickname,
            'rivalAvatarUrl' => $this->getAvatarUrl($game->avatar_filename ?? null),
            'createdAt'      => $game->created_at?->getTimestamp(),
            'endedAt'        => $game->ended_at?->getTimestamp(),
        ];
    }

    private function getAvatarUrl(?string $avatarFilename): ?string
    {
        return $avatarFilename
            ? (new AvatarManager())->getUrl($avatarFilename)
            : null;
    }
}
