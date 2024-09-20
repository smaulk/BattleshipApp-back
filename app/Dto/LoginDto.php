<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\LoginRequest;
use App\Parents\Dto;

final readonly class LoginDto extends Dto
{
    public string $email;
    public string $password;
    public ?string $ipAddress;
    public ?string $userAgent;

    public static function fromRequest(LoginRequest $request): self
    {
        $dto = new self();
        $dto->email = $request->validated('email');
        $dto->password = $request->validated('password');
        $dto->ipAddress = $request->ip();
        $dto->userAgent = $request->userAgent();
        return $dto;
    }
}
