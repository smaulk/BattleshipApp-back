<?php
declare(strict_types=1);

namespace App\Services;

use App\Classes\AvatarManager;
use App\Dto\UpdateUserAvatarDto;
use App\Exceptions\HttpException;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class UpdateUserAvatarService extends Service
{
    /**
     * Обновляет аватар пользователя, сохраняет файл в хранилище и обновляет имя файла
     */
    public function run(UpdateUserAvatarDto $dto): void
    {
        $avatarManager = new AvatarManager();

        try {
            $user = $this->getUserById($dto->userId);
            $oldFilename = $user->avatar_filename;
            $newFilename = $avatarManager->save($dto->avatar);
            $user->avatar_filename = $newFilename;
            $user->saveOrFail();
        } catch (ModelNotFoundException) {
            throw new HttpException(404, 'Пользователь не найден');
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
