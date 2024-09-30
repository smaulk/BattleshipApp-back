<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\GetUsersByFriendshipDto;
use App\Dto\PaginateDto;
use App\Enums\FriendshipStatus;
use App\Enums\FriendshipType;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Database\Eloquent\Collection;

final class GetUsersByFriendshipService extends PaginateService
{
    /**
     * Вовзращает список друзей пользователя с пагинацией, сортируя по новизне
     * @param GetUsersByFriendshipDto $dto
     * @return PaginateDto
     */
    public function run(GetUsersByFriendshipDto $dto): PaginateDto
    {
        [$status1, $status2] = $this->getStatusPair($dto->type);
        $friends = $this->fetchFriends($dto, $status1, $status2);

        return $this->paginate($friends);
    }

    /**
     * Возвращает пару FriendshipStatus по FriendshipType
     */
    private function getStatusPair(FriendshipType $type): array
    {
        return match ($type) {
            FriendshipType::FRIEND   => [FriendshipStatus::FRIEND, FriendshipStatus::FRIEND],
            FriendshipType::OUTGOING => [FriendshipStatus::REQ_UID1, FriendshipStatus::REQ_UID2],
            FriendshipType::INCOMING => [FriendshipStatus::REQ_UID2, FriendshipStatus::REQ_UID1],
        };
    }

    /**
     * Возвращает коллекцию друзей пользователя
     */
    private function fetchFriends(GetUsersByFriendshipDto $dto, FriendshipStatus $status1, FriendshipStatus $status2): Collection
    {
        return User::query()
            ->select([
                'users.id',
                'users.nickname',
                'users.avatar_filename',
                "friendships.id as {$this->getPaginateId()}"
            ]) // Выбираем поля из таблицы users и id записи friendship
            ->join(
                'friendships',
                function ($join) use ($dto, $status1, $status2) {
                    $join
                        ->on('users.id', '=', 'friendships.uid2')
                        ->where('friendships.uid1', $dto->userId)
                        ->where('friendships.status', $status1)
                        ->orWhere(
                            function ($query) use ($dto, $status2) {
                                $query->on('users.id', '=', 'friendships.uid1')
                                    ->where('friendships.uid2', $dto->userId)
                                    ->where('friendships.status', $status2);
                            }
                        );
                })
            ->when(
                $dto->startId !== null,
                function ($query) use ($dto) {
                    $query->where('friendships.id', '<', $dto->startId); // Фильтруем по ID в friendships
                }
            )
            ->when(
                $dto->nickname !== null,
                function ($query) use ($dto) {
                    $query->where('users.nickname', 'like', "$dto->nickname%");
                }
            )
            ->orderByDesc('friendships.id')
            ->limit(self::LIMIT) // Ограничиваем количество записей
            ->get(); // Добавляем 1 для проверки наличия следующих записей
    }

    protected function getPaginateId(): string
    {
        return 'friendshipId';
    }
}