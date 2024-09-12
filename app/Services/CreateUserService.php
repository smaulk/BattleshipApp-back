<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateUserDto;
use App\Exceptions\HttpException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateUserService
{
    public function run(CreateUserDto $dto): User
    {
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
}
