<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\VerifyEmailRequest;
use App\Parents\Dto;

final readonly class VerifyEmailDto extends Dto
{
    public int $userId;
    public string $id;
    public string $hash;

    public static function fromRequest(VerifyEmailRequest $request): self
    {
        $dto = new self();
        $dto->userId = (int)$request->route('userId');
        $dto->id = (string)$request->validated('id');
        $dto->hash = (string)$request->validated('hash');
        return $dto;
    }
}