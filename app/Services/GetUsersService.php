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
                'id',
                'nickname',
                'avatar_filename'
            ])
            ->whereNot('id', $dto->userId)
            ->when(
                $dto->startId !== null,
                function ($query) use ($dto) {
                    $query->where('id', '>', $dto->startId); // Фильтруем по ID
                }
            )
            ->where('nickname', 'like', "$dto->nickname%")
            ->orderBy('id')
            ->limit(self::LIMIT)
            ->get();
    }

    protected function getPaginateId(): string
    {
        return 'id';
    }
}
