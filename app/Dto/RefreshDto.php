<?php

namespace App\Dto;

use App\Http\Requests\RefreshRequest;

final readonly class RefreshDto extends Dto
{
    public string $ulid;
    public ?string $ipAddress;
    public ?string $userAgent;

    public static function fromRequest(RefreshRequest $request): self
    {
        $dto = new self();
        $dto->ulid = $request->validated('refreshToken');
        $dto->ipAddress = $request->ip();
        $dto->userAgent = $request->userAgent();
        return $dto;
    }
}