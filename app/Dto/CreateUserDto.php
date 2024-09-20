<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\CreateUserRequest;
use App\Parents\Dto;

final readonly class CreateUserDto extends Dto
{
    public string $nickname;
    public string $email;
    public string $password;

    public static function fromRequest(CreateUserRequest $request): self
    {
        $dto = new self();
        $dto->nickname = $request->validated('nickname');
        $dto->email = $request->validated('email');
        $dto->password = $request->validated('password');
        return $dto;
    }
}
