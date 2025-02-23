<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\GameType;
use App\Models\Game;
use App\Models\UserStatistic;
use App\Parents\Service;
use Illuminate\Support\Facades\DB;

final class UpdateStatisticsService extends Service
{
    public function run(Game $game): void
    {
        UserStatistic::query()
            ->whereIn('user_id', [$game->uid1, $game->uid2])
            ->update($this->getQueryData(
                $this->getStatisticData($game)
            ));
    }

    private function getQueryData(array $data): array
    {
        $fields = ['wins', 'losses', 'draws'];

        return array_reduce($fields, function ($query, $field) use ($data) {
            $query[$field] = DB::raw("$field + CASE user_id 
                WHEN {$data['uid1']} THEN {$data[$field . '1']} 
                WHEN {$data['uid2']} THEN {$data[$field . '2']} 
            ELSE 0 END");
            return $query;
        }, [
            'games'      => DB::raw('games + 1'),
            'updated_at' => now(),
            'points'     => DB::raw("GREATEST(points + CASE user_id 
                WHEN {$data['uid1']} THEN {$data['points1']} 
                WHEN {$data['uid2']} THEN {$data['points2']} 
            ELSE 0 END, 0)"),
        ]);
    }

    private function getStatisticData(Game $game): array
    {
        $data = [];
        foreach ([1 => $game->uid1, 2 => $game->uid2] as $key => $id) {
            $type = $game->status->toType($game->uid1 === $id);
            $data += [
                "uid$key"          => $id,
                "wins$key"         => (int)($type === GameType::WIN),
                "losses$key"       => (int)($type === GameType::LOSE),
                "draws$key"        => (int)($type === GameType::DRAW),
                "points$key"       => UserStatistic::getPointValue($type),
            ];
        }

        return $data;
    }
}