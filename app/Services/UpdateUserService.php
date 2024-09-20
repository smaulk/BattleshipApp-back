<?php
declare(strict_types=1);

namespace App\Services;

use App\Classes\AvatarManager;
use App\Dto\UpdateUserDto;
use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

final class UpdateUserService extends Service
{
    /**
     * Обновляет данные пользователя
     * @throws Throwable
     */
    public function run(UpdateUserDto $dto): User
    {
        $this->validate($dto);
        $user = User::query()->findOrFail($dto->userId);
        $this->updateUser($user, $dto);

        return $user;
    }

    /**
     * @throws Throwable
     */
    private function updateUser(User $user, UpdateUserDto $dto): void
    {
        $user->nickname = $dto->nickname;
        $user->email = $dto->email;
        $user->saveOrFail();
    }

    private function validate(UpdateUserDto $dto): void
    {
        $users = $this->getUserDataMatches($dto);
        if ($users->where('nickname', $dto->nickname)->isNotEmpty()) {
            throw new HttpException(422, 'Имя пользователя уже используется');
        }
        if ($users->where('email', $dto->email)->isNotEmpty()) {
            throw new HttpException(422, 'Электронная почта уже используется');
        }

    }

    private function getUserDataMatches(UpdateUserDto $dto): Collection
    {
        return User::query()
            ->select([
                'nickname',
                'email',
            ])
            ->whereNot('id', $dto->userId)
            ->where(function ($query) use ($dto) {
                $query->where('nickname', $dto->nickname)
                    ->orWhere('email', $dto->email);
            })
            ->get();
    }


}
