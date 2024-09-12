<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\LoginRequest;

final readonly class LoginDto extends Dto
{
    public string $nickname;
    public string $password;
    public ?string $ipAddress;
    public ?string $userAgent;

    public static function fromRequest(LoginRequest $request): self
    {
        $dto = new self();
        $dto->nickname = $request->validated('nickname');
        $dto->password = $request->validated('password');
        $dto->ipAddress = $request->ip();
        $dto->userAgent = $request->userAgent();
        return $dto;
    }
}
