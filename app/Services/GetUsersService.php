<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\GetUsersDto;
use App\Dto\PaginateDto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class GetUsersService extends PaginateService
{
    public function run(GetUsersDto $dto): PaginateDto
    {
        $users = $this->fetchUsers($dto);
        return $this->paginate($users);
    }

    private function fetchUsers(GetUsersDto $dto): Collection
    {
        return User::query()
            ->select([
                'users.id',
                'users.nickname',
                'users.avatar_filename',
                'friendships.status'
            ])
            ->joinFriendships($dto->userId)
            ->whereNot('users.id', $dto->userId)
            ->when(!empty($dto->startId), function ($query) use ($dto) {
                $query->where('users.id', '>', $dto->startId); // Фильтруем по ID
            })
            ->where('users.nickname', 'like', "$dto->nickname%")
            ->orderBy($this->getPaginateId())
            ->limit($this->getLimit())
            ->get();
    }

    protected function getPaginateId(): string
    {
        return 'id';
    }
}
