<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\GetUsersByFriendshipDto;
use App\Dto\PaginateDto;
use App\Models\User;
use App\Services\Abstract\PaginateService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;

final class GetUsersByFriendshipService extends PaginateService
{
    /**
     * Возвращает список друзей пользователя с пагинацией, сортируя по новизне
     * @param GetUsersByFriendshipDto $dto
     * @return PaginateDto
     */
    public function run(GetUsersByFriendshipDto $dto): PaginateDto
    {
        return $this->paginate($this->fetchFriendshipUsers($dto));
    }

    /**
     * Возвращает коллекцию друзей пользователя
     */
    private function fetchFriendshipUsers(GetUsersByFriendshipDto $dto): Collection
    {
        [$status1, $status2] = $dto->type->toStatuses();

        return User::query()
            ->select([
                'users.id',
                'users.nickname',
                'users.avatar_filename',
                'users.is_online',
                "friendships.id as {$this->getPaginateId()}"
            ]) // Выбираем поля из таблицы users и id записи friendships
            ->join('friendships', function (JoinClause $join) use ($dto, $status1, $status2) {
                $join->on('users.id', '=', 'friendships.uid2')
                    ->where('friendships.uid1', $dto->userId)
                    ->where('friendships.status', $status1)
                    ->orWhere(
                        function (JoinClause $join) use ($dto, $status2) {
                            $join->on('users.id', '=', 'friendships.uid1')
                                ->where('friendships.uid2', $dto->userId)
                                ->where('friendships.status', $status2);
                        }
                    );
            })
            ->when(!empty($dto->startId), function ($query) use ($dto) {
                $query->where('friendships.id', '<', $dto->startId); // Фильтруем по ID в friendships
            })
            ->when(!empty($dto->nickname), function ($query) use ($dto) {
                $query->where('users.nickname', 'like', "$dto->nickname%");
            })
            ->when(!is_null($dto->isOnline), function ($query) use ($dto) {
                $query->where('users.is_online', $dto->isOnline);
            })
            ->orderByDesc($this->getPaginateId())
            ->limit($this->getLimit())
            ->get();
    }

    protected function getPaginateId(): string
    {
        return 'friendshipId';
    }
}