<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\ResetPasswordRequest;
use App\Parents\Dto;

final readonly class ResetPasswordDto extends Dto
{
    public string $token;
    public string $email;
    public string $password;

    public static function fromRequest(ResetPasswordRequest $request): self
    {
        $dto = new self();
        $dto->token = $request->validated('token');
        $dto->email = $request->validated('email');
        $dto->password = $request->validated('password');
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}