<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\UpdateUserDto;
use App\Exceptions\HttpException;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

final class UpdateUserService extends Service
{
    /**
     * Обновляет данные пользователя
     */
    public function run(UpdateUserDto $dto): User
    {
        $this->validate($dto);

        try {
            $user = $this->getUserById($dto->id);
            $this->updateUser($user, $dto);
        } catch (ModelNotFoundException) {
            throw new HttpException(404, 'Пользователь не найден');
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500);
        }

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
            ->whereNot('id', $dto->id)
            ->where(function ($query) use ($dto) {
                $query->where('nickname', $dto->nickname)
                    ->orWhere('email', $dto->email);
            })
            ->get();
    }

    /**
     * Получить пользователя по id
     * @throws ModelNotFoundException
     */
    private function getUserById(int $userId): User
    {
        /** @var User */
        return User::query()->findOrFail($userId);
    }
}
