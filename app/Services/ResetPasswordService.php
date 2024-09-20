<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\ResetPasswordDto;
use App\Exceptions\HttpException;
use App\Models\RefreshToken;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

final class ResetPasswordService extends Service
{
    public function run(ResetPasswordDto $dto): void
    {
        $status = Password::reset($dto->toArray(), function (User $user, string $password) {
            $user->password = Hash::make($password);
            $user->saveOrFail();

            $this->blockUserSessions($user->id);
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw new HttpException(500);
        }
    }

    /**
     * Блокирует все сессии пользователя
     */
    private function blockUserSessions(int $userId): void
    {
        RefreshToken::query()
            ->where('user_id', $userId)
            ->update([
                'is_blocked' => 1,
            ]);
    }
}