<?php
declare(strict_types=1);

namespace App\Services;

use App\Classes\AvatarManager;
use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Support\Facades\Log;
use Throwable;

final class DeleteUserAvatarService extends Service
{
    /**
     * @throws Throwable
     */
    public function run(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $filename = $user->avatar_filename;
        $user->avatar_filename = null;
        $user->saveOrFail();

        (new AvatarManager())->delete($filename);
    }
}
