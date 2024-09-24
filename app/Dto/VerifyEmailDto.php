<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\VerifyEmailRequest;
use App\Parents\Dto;

final readonly class VerifyEmailDto extends Dto
{
    public int $userId;
    public string $hash;
    public int $exp;
    public string $signature;

    public static function fromRequest(VerifyEmailRequest $request): self
    {
        $dto = new self();
        $dto->userId = (int)$request->route('userId');
        $dto->hash = (string)$request->validated('hash');
        $dto->exp = (int)$request->validated('expiresAt');
        $dto->signature = (string)$request->validated('signature');
        return $dto;
    }
}