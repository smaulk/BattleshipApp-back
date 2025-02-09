<?php

namespace App\Http\Resources;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Game $game */
        $game = $this->resource;

        return [
            'id'         => $game->id,
            'type'       => $game->status->toType($game->uid1 !== $game->rivalId),
            'rivalId'    => $game->rivalId,
            'nickname'   => $game->nickname,
            'created_at' => $game->created_at,
            'ended_at'   => $game->ended_at,
        ];
    }
}
