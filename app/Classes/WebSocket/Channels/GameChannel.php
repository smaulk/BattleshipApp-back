<?php
declare(strict_types=1);

namespace App\Classes\WebSocket\Channels;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use App\Parents\Channel;
use Illuminate\Database\Eloquent\Builder;

class GameChannel extends Channel
{
    public function join(User $user, int $gameId): bool
    {
        return Game::query()
            ->where('id', $gameId)
            ->where('status', GameStatus::CREATED)
            ->where(function (Builder $query) use ($user) {
                $query->where('uid1', $user->id)
                    ->orWhere('uid2', $user->id);
            })
            ->exists();
    }
}