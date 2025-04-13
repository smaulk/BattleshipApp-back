<?php
declare(strict_types=1);

namespace App\Services;

use App\Classes\AvatarManager;
use App\Dto\UpdateUserAvatarDto;
use App\Exceptions\HttpException;
use App\Models\User;
use App\Parents\Service;
use Illuminate\Support\Facades\Log;
use Throwable;

final class UpdateUserAvatarService extends Service
{
    /**
     * Обновляет аватар пользователя, сохраняет файл в хранилище и обновляет имя файла
     */
    public function run(UpdateUserAvatarDto $dto): void
    {
        $avatarManager = new AvatarManager();
        $user = User::query()->findOrFail($dto->userId);

        $oldFilename = $user->avatar_filename;
        $newFilename = $avatarManager->save($dto->avatar);

        try {
            $user->avatar_filename = $newFilename;
            $user->saveOrFail();
        } catch (Throwable $exception) {
            if (!empty($newFilename)) {
                $avatarManager->delete($newFilename);
            }
            Log::error($exception);
            throw new HttpException(500);
        }

        if ($oldFilename) {
            $avatarManager->delete($oldFilename);
        }
    }
}
