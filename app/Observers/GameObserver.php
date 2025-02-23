<?php

namespace App\Observers;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Parents\Observer;
use App\Services\UpdateStatisticsService;

final class GameObserver extends Observer
{
    public function creating(Game $game): void
    {
        $game->status = $game->status ?? GameStatus::CREATED;

        if ($game->is_ended) {
            $game->ended_at = now();
        }
    }

    public function created(Game $game): void
    {
        if ($game->is_ended) {
            (new UpdateStatisticsService())->run($game);
        }
    }

    public function updating(Game $game): void
    {
        if ($game->isDirty('status') && $game->is_ended) {
            $game->ended_at = now();
        }
    }

    public function updated(Game $game): void
    {
        //  Если игра завершена, то записываем в статистику
        if ($game->isDirty('status') && $game->getOriginal('status') === GameStatus::CREATED) {
            (new UpdateStatisticsService())->run($game);
        }
    }
}
