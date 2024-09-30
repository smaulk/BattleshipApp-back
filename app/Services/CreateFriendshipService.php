<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateFriendshipDto;
use App\Enums\FriendshipStatus;
use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class CreateFriendshipService extends Service
{
    public function run(CreateFriendshipDto $dto)
    {
        if ($dto->userId === $dto->friendId) {
            throw new HttpException(400, 'Идентификаторы пользователей совпадают');
        }

        if (
            !User::query()->where('id', $dto->userId)->exists() ||
            !User::query()->where('id', $dto->friendId)->exists()
        ) {
            throw new HttpException(400, User::getNotFoundMessage());
        }

        if ($this->isExistsFriendship($dto->userId, $dto->friendId)) {
            throw new HttpException(400, 'Запись уже существует');
        }

        if (!$this->createFriendRequest($dto->userId, $dto->friendId)) {
            throw new HttpException(500);
        }
    }

    private function isExistsFriendship(int $uid1, int $uid2): bool
    {
        return $this->findFriendship($uid1, $uid2)->exists();
    }

    private function findFriendship(int $uid1, int $uid2): Builder
    {
        [$minId, $maxId] = sort_nums($uid1, $uid2);
        return DB::table('friendships')
            ->where('uid1', $minId)
            ->where('uid2', $maxId);
    }

    private function createFriendRequest(int $uid1, int $uid2): bool
    {
        [$minId, $maxId] = sort_nums($uid1, $uid2);
        $status = $minId === $uid1
            ? FriendshipStatus::REQ_UID1
            : FriendshipStatus::REQ_UID2;

        return DB::table('friendships')
            ->insert([
                'uid1'       => $minId,
                'uid2'       => $maxId,
                'status'     => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }
}