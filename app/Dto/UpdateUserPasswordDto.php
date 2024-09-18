<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\UpdateUserPasswordRequest;

final readonly class UpdateUserPasswordDto extends Dto
{
    public int $userId;
    public string $currentPassword;
    public string $newPassword;

    public static function fromRequest(UpdateUserPasswordRequest $request): self
    {
        $dto = new self();
        $dto->userId = (int)$request->route('userId');
        $dto->currentPassword = $request->validated('current_password');
        $dto->newPassword = $request->validated('new_password');
        return $dto;
    }
}