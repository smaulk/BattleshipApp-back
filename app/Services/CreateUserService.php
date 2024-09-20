<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateUserDto;
use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Throwable;

final class CreateUserService extends Service
{
    /**
     * Создает нового пользователя
     * @throws Throwable
     */
    public function run(CreateUserDto $dto): User
    {
        $this->validate($dto);

        return $this->createUser($dto);
    }

    /**
     * Создает нового пользователя
     * @throws Throwable
     */
    private function createUser(CreateUserDto $dto): User
    {
        $user = new User();
        $user->nickname = $dto->nickname;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->saveOrFail();

        return $user;
    }

    /**
     * Проверяет данные DTO на корректность
     */
    private function validate(CreateUserDto $dto): void
    {
        $users = $this->getUserDataMatches($dto);
        if ($users->where('nickname', $dto->nickname)->isNotEmpty()) {
            throw new HttpException(422, 'Имя пользователя уже используется');
        }
        if ($users->where('email', $dto->email)->isNotEmpty()) {
            throw new HttpException(422, 'Электронная почта уже используется');
        }
    }

    /**
     * Возвращает пользователей, у которых совпадают nickname или email с данными из DTO
     */
    private function getUserDataMatches(CreateUserDto $dto): Collection
    {
        return User::query()
            ->select([
                'nickname',
                'email'
            ])
            ->where('nickname', $dto->nickname)
            ->orWhere('email', $dto->email)
            ->get();
    }
}
