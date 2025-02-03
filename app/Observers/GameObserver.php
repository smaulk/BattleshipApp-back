<?php

namespace App\Observers;

use App\Enums\GameStatus;
use App\Models\Game;
use Carbon\Carbon;

class GameObserver
{
    public function creating(Game $game): void
    {
        $game->status = $game->status ?? GameStatus::CREATED;
        // Проверяем, что статус не CREATED, и если так, то устанавливаем ended_at
        if ($game->status !== GameStatus::CREATED) {
            $game->ended_at = Carbon::now();
        }
    }

    public function created(Game $game): void
    {
        //
    }

    public function updating(Game $game): void
    {
        // Проверяем, что статус изменился и он больше не CREATED
        if ($game->isDirty('status') && $game->status !== GameStatus::CREATED) {
            $game->ended_at = Carbon::now();
        }
    }

    public function updated(Game $game): void
    {
        //
    }

    public function deleted(Game $game): void
    {
        //
    }

    public function restored(Game $game): void
    {
        //
    }

    public function forceDeleted(Game $game): void
    {
        //
    }
}
