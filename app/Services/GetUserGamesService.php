<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\GetUserGamesDto;
use App\Dto\PaginateDto;
use App\Models\Game;
use App\Services\Abstract\PaginateService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;

final class GetUserGamesService extends PaginateService
{
    public function run(GetUserGamesDto $dto): PaginateDto
    {
        return $this->paginate($this->fetchGames($dto));
    }

    public function fetchGames(GetUserGamesDto $dto): Collection
    {
        [$status1, $status2] = $dto->type?->toStatuses() ?? [null, null];

        return Game::query()
            ->select([
                'games.id',
                'games.uid1',
                'games.uid2',
                'games.status',
                'games.created_at',
                'games.ended_at',
                'users.id as rivalId',
                'users.nickname'
            ])
            ->join('users', function (JoinClause $join) use ($dto, $status1, $status2) {
                $join->on('games.uid2', '=', 'users.id')
                    ->where('games.uid1', $dto->userId)
                    ->when(!empty($status1), function ($query) use ($status1) {
                        $query->where('games.status', $status1);
                    })
                    ->orWhere(function (JoinClause $join) use ($dto, $status2) {
                        $join->on('games.uid1', '=', 'users.id')
                            ->where('games.uid2', $dto->userId)
                            ->when(!empty($status2), function ($query) use ($status2) {
                                $query->where('games.status', $status2);
                            });
                    });
            })
            ->when(!empty($dto->startId), function ($query) use ($dto) {
                $query->where('games.id', '<', $dto->startId);
            })
            ->orderByDesc($this->getPaginateId())
            ->limit($this->getLimit())
            ->get();
    }


    protected function getPaginateId(): string
    {
        return 'id';
    }
}