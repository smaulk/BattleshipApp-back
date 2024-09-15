<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\UpdateUserAvatarRequest;
use Illuminate\Http\UploadedFile;

class UpdateUserAvatarDto
{
    public int $userId;
    public UploadedFile $avatar;

    public static function fromRequest(UpdateUserAvatarRequest $request): UpdateUserAvatarDto
    {
        $dto = new self();
        $dto->userId = (int)$request->route('userId');
        $dto->avatar = $request->validated('avatar');
        return $dto;
    }
}
