<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\GetUsersDto;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Support\Collection;

final class GetUsersService extends Service
{
    public function run(GetUsersDto $dto): Collection
    {
        return User::query()
            ->select([
                'id',
                'nickname',
                'avatar_filename'
            ])
            ->whereNot('id', $dto->userId)
            ->where('nickname', 'like', "$dto->nickname%")
            ->orderByDesc('id')
            ->get();
    }
}
