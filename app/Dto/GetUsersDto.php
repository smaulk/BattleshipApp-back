<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\GetUsersRequest;
use App\Parents\Dto;

final readonly class GetUsersDto extends Dto
{

    public int $userId;
    public string $nickname;
    public ?int $startId;

    public static function fromRequest(GetUsersRequest $request): GetUsersDto
    {
        $startId = $request->validated('startId');

        $dto = new self();
        $dto->userId = $request->user()?->getAuthIdentifier();
        $dto->nickname = $request->validated('nickname');
        $dto->startId = $startId ? (int)$startId : null;
        return $dto;
    }
}
