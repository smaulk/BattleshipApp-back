<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateUserDto;
use App\Exceptions\HttpException;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateUserService
{
    public function run(CreateUserDto $dto): User
    {
        $this->validate($dto);

        try {
            return $this->createUser($dto);
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500);
        }
    }

    /**
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
