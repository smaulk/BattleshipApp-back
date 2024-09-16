<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\UpdateUserRequest;

final readonly class UpdateUserDto extends Dto
{
    public int $id;
    public string $nickname;
    public string $email;

    public static function fromRequest(UpdateUserRequest $request): UpdateUserDto
    {
        $dto = new self();
        $dto->id = (int)$request->route('userId');
        $dto->nickname = $request->validated('nickname');
        $dto->email = $request->validated('email');
        return $dto;
    }
}